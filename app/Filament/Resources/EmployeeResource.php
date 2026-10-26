<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Çalışan';
    protected static ?string $pluralModelLabel = 'Çalışanlar';
    protected static ?string $navigationLabel = 'Çalışanlar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kişisel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->label('Ad'),

                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->label('Soyad'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('İletişim ve Durum')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('E-Posta Adresi'),

                        Forms\Components\TextInput::make('department')
                            ->label('Departman'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif Çalışan mı?')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable()
                    ->label('Ad'),

                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->label('Soyad'),

                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),

                Tables\Columns\TextColumn::make('department')
                    ->badge()
                    ->color('info')
                    ->label('Departman'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Durum'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),

                // JSON IMPORT
                Tables\Actions\Action::make('import_json')
                    ->label('JSON Yükle (Import)')
                    ->authorize(fn (): bool => auth()->user()?->hasRole('super_admin') ?? false)
                    ->icon('heroicon-m-arrow-up-tray')
                    ->color('warning')
                    ->form([
                        Forms\Components\FileUpload::make('dosya')
                            ->label('JSON Dosyasını Seçin')
                            ->acceptedFileTypes(['application/json'])
                            ->disk('local')
                            ->directory('imports')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $disk = Storage::disk('local');
                        $path = $data['dosya'];

                        try {
                            $jsonContent = $disk->get($path);

                            $kayitlar = json_decode(
                                $jsonContent,
                                true,
                                512,
                                JSON_THROW_ON_ERROR
                            );

                            if (!is_array($kayitlar) || !array_is_list($kayitlar)) {
                                throw new \InvalidArgumentException(
                                    'JSON dosyası çalışan kayıtlarından oluşan bir liste içermelidir.'
                                );
                            }

                            // JSON içindeki tüm kayıtları doğrula.
                            $validator = Validator::make(
                                ['employees' => $kayitlar],
                                [
                                    'employees' => ['required', 'array', 'min:1'],

                                    'employees.*.first_name' => [
                                        'required', 'string', 'max:255',
                                    ],
                                    'employees.*.last_name' => [
                                        'required', 'string', 'max:255',
                                    ],
                                    'employees.*.email' => [
                                        'required', 'email', 'max:255', 'distinct',
                                    ],
                                    'employees.*.department' => [
                                        'nullable', 'string', 'max:255',
                                    ],

                                    'employees.*.assets' => [
                                        'sometimes', 'array',
                                    ],
                                    'employees.*.assets.*.name' => [
                                        'required', 'string', 'max:255',
                                    ],
                                    'employees.*.assets.*.serial_number' => [
                                        'required', 'string', 'max:255',
                                    ],
                                    'employees.*.assets.*.type' => [
                                        'required',
                                        'in:laptop,monitor,phone,peripheral',
                                    ],

                                    'employees.*.projects' => [
                                        'sometimes', 'array',
                                    ],
                                    'employees.*.projects.*' => [
                                        'required', 'string', 'max:255',
                                    ],
                                ]
                            );

                            $validator->validate();

                            // Hata oluşursa tüm veritabanı değişiklikleri geri alınır.
                            $sayac = DB::transaction(function () use ($kayitlar): int {
                                $count = 0;

                                foreach ($kayitlar as $kayit) {
                                    $calisan = Employee::updateOrCreate(
                                        ['email' => $kayit['email']],
                                        [
                                            'first_name' => $kayit['first_name'],
                                            'last_name' => $kayit['last_name'],
                                            'department' => $kayit['department'] ?? null,
                                        ]
                                    );

                                    foreach ($kayit['assets'] ?? [] as $assetData) {
                                        $asset = Asset::where(
                                            'serial_number',
                                            $assetData['serial_number']
                                        )->lockForUpdate()->first();

                                        // Başka bir çalışana atanmış cihazı taşıma.
                                        if (
                                            $asset !== null &&
                                            $asset->employee_id !== $calisan->id
                                        ) {
                                            throw new \RuntimeException(
                                                'Seri numarası çakışması: ' .
                                                $assetData['serial_number']
                                            );
                                        }

                                        Asset::updateOrCreate(
                                            [
                                                'serial_number' =>
                                                    $assetData['serial_number'],
                                            ],
                                            [
                                                'name' => $assetData['name'],
                                                'type' => $assetData['type'],
                                                'employee_id' => $calisan->id,
                                                'is_available' => false,
                                            ]
                                        );
                                    }

                                    foreach ($kayit['projects'] ?? [] as $projeAdi) {
                                        $proje = Project::firstOrCreate([
                                            'title' => $projeAdi,
                                        ]);

                                        $calisan->projects()->syncWithoutDetaching([
                                            $proje->id,
                                        ]);
                                    }

                                    $count++;
                                }

                                return $count;
                            });

                            Notification::make()
                                ->title(
                                    "$sayac çalışan başarıyla içeri aktarıldı."
                                )
                                ->success()
                                ->send();

                        } catch (ValidationException $e) {
                            Notification::make()
                                ->title('JSON doğrulama hatası')
                                ->body(
                                    implode(
                                        ' ',
                                        array_slice(
                                            $e->validator->errors()->all(),
                                            0,
                                            5
                                        )
                                    )
                                )
                                ->danger()
                                ->send();

                        } catch (Throwable $e) {
                            report($e);

                            Notification::make()
                                ->title('JSON içeri aktarılamadı')
                                ->body(
                                    'Dosya biçimini ve mevcut demirbaş kayıtlarını kontrol edin.'
                                )
                                ->danger()
                                ->send();

                        } finally {
                            $disk->delete($path);
                        }
                    }),

                // JSON EXPORT
                Tables\Actions\Action::make('export_json')
                    ->label('JSON Dışa Aktar (Export)')
                    ->authorize(fn (): bool => auth()->user()?->hasRole('super_admin') ?? false)
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return response()->streamDownload(function () {
                            $veriler = Employee::with([
                                'assets',
                                'projects',
                            ])->get();

                            $temizVeri = $veriler->map(function ($calisan) {
                                return [
                                    'first_name' => $calisan->first_name,
                                    'last_name' => $calisan->last_name,
                                    'email' => $calisan->email,
                                    'department' => $calisan->department,

                                    'assets' => $calisan->assets->map(
                                        fn ($asset) => [
                                            'name' => $asset->name,
                                            'serial_number' => $asset->serial_number,
                                            'type' => $asset->type,
                                        ]
                                    )->values()->all(),

                                    'projects' => $calisan->projects
                                        ->pluck('title')
                                        ->values()
                                        ->all(),
                                ];
                            });

                            echo json_encode(
                                $temizVeri->values()->all(),
                                JSON_PRETTY_PRINT |
                                JSON_UNESCAPED_UNICODE |
                                JSON_THROW_ON_ERROR
                            );
                        }, 'sirket_verisi_' . date('Y-m-d') . '.json', [
                            'Content-Type' => 'application/json; charset=UTF-8',
                        ]);
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AssetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}