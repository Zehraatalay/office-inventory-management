<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected static ?string $title = 'Zimmetli Eşyalar';

    // Demirbaş oluşturma ve düzenleme formu
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Ürün Adı'),

                Forms\Components\Select::make('type')
                    ->options([
                        'laptop' => 'Laptop',
                        'monitor' => 'Monitör',
                        'phone' => 'Telefon',
                        'peripheral' => 'Klavye/Mouse',
                    ])
                    ->required()
                    ->label('Cihaz Tipi'),

                Forms\Components\TextInput::make('serial_number')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Seri Numarası'),
            ]);
    }

    // Demirbaş listeleme tablosu
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Görsel'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Ürün Adı')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->label('Tip'),

                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Seri Numarası')
                    ->searchable(),
            ])

            // Tablo üstündeki butonlar
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Demirbaş Oluştur ve Ata'),

                // Yalnızca depodaki demirbaşlar atanabilir.
                Tables\Actions\AssociateAction::make()
                    ->label('Depodan Ata')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(
                        fn (Builder $query): Builder => $query
                            ->whereNull('employee_id')
                            ->where('is_available', true)
                    ),

                // Zimmet tutanağını yeni sekmede aç.
                Tables\Actions\Action::make('tutanak_yazdir')
                    ->label('Zimmet Tutanağı (PDF)')
                    ->icon('heroicon-m-printer')
                    ->color('gray')
                    ->url(
                        fn ($livewire) => route(
                            'zimmet.tutanagi',
                            ['id' => $livewire->ownerRecord->id]
                        )
                    )
                    ->openUrlInNewTab(),

                // Zimmet detay sayfasına git.
                Tables\Actions\Action::make('panelde_gor')
                    ->label('Panelde İncele')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(
                        fn ($livewire) => \App\Filament\Pages\ZimmetDetay::getUrl([
                            'record' => $livewire->ownerRecord->id,
                        ])
                    ),
            ])

            // Her demirbaş için yapılabilecek işlemler
            ->actions([

                // 1. Tamire gönder
                Tables\Actions\Action::make('tamir')
                    ->label('Tamire Gönder')
                    ->icon('heroicon-m-wrench')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cihazı Tamire Gönder')
                    ->modalDescription('Bu cihazın arızalandığını onaylıyor musunuz?')
                    ->modalSubmitActionLabel('Evet, Gönder')
                    ->visible(fn (Asset $record): bool => $record->is_available)
                    ->action(function (Asset $record): void {
                        $record->update([
                            'is_available' => false,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Cihaz Teknik Servise Yönlendirildi')
                            ->success()
                            ->send();
                    }),

                // 2. Sorun bildir
                Tables\Actions\Action::make('sorun_bildir')
                    ->label('Sorun Bildir')
                    ->icon('heroicon-m-chat-bubble-bottom-center-text')
                    ->color('warning')
                    ->form([
                        Forms\Components\Textarea::make('aciklama')
                            ->label('Sorunun Detayı')
                            ->placeholder('Örn: Ekran bazen gidip geliyor...')
                            ->required()
                            ->rows(3),

                        Forms\Components\Select::make('oncelik')
                            ->label('Aciliyet Durumu')
                            ->options([
                                'low' => 'Düşük',
                                'medium' => 'Orta',
                                'high' => 'Yüksek (Acil)',
                            ])
                            ->default('medium')
                            ->required(),
                    ])
                    ->action(function (Asset $record, array $data): void {
                        \Filament\Notifications\Notification::make()
                            ->title('Sorun Bildirimi Alındı')
                            ->body(
                                'Cihaz: ' . $record->name .
                                ' | Sorun: ' . $data['aciklama'] .
                                ' | Aciliyet: ' . $data['oncelik']
                            )
                            ->warning()
                            ->send();
                    }),

                // 3. Demirbaş kopyala
                Tables\Actions\ReplicateAction::make()
                    ->label('Kopyasını Oluştur')
                    ->icon('heroicon-m-document-duplicate')
                    ->color('info')
                    ->modalHeading('Ürünü Çoğalt')
                    ->modalDescription(
                        'Bu ürünün bir kopyası oluşturulacak. Yeni ürün depoda olacak.'
                    )
                    ->beforeReplicaSaved(function (Asset $replica): void {

                        // UUID kullanarak seri numarası çakışma riskini azalt.
                        $replica->serial_number = 'COPY-' . Str::uuid();

                        // Kopya herhangi bir çalışana atanmasın.
                        $replica->employee_id = null;

                        // Kopya kullanılabilir durumda oluşturulsun.
                        $replica->is_available = true;

                        // Ürün adını ayırt edilebilir hâle getir.
                        $replica->name = $replica->name . ' (Kopya)';
                    })
                    ->successNotificationTitle('Ürün Başarıyla Çoğaltıldı'),

                // 4. Düzenle
                Tables\Actions\EditAction::make(),

                // 5. İade al
                Tables\Actions\DissociateAction::make()
                    ->label('İade Al'),

                // 6. Sil
                Tables\Actions\DeleteAction::make(),
            ])

            // Birden fazla demirbaş üzerinde yapılabilecek işlemler
            ->bulkActions([

                Tables\Actions\DeleteBulkAction::make(),

                // Toplu iade
                Tables\Actions\DissociateBulkAction::make()
                    ->label('Seçilileri İade Al')
                    ->icon('heroicon-m-arrow-left-on-rectangle'),

                // Toplu tamire gönderme
                Tables\Actions\BulkAction::make('toplu_tamir')
                    ->label('Seçilileri Tamire Yolla')
                    ->icon('heroicon-m-wrench')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        Asset::whereIn('id', $records->pluck('id'))
                            ->update([
                                'is_available' => false,
                            ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Seçilen cihazlar teknik servise yönlendirildi')
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }
}