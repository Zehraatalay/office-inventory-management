<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ürün Bilgileri')
                    ->schema([
                        // image yukleme alani
                        Forms\Components\FileUpload::make('image')
                            ->image() // sadece image kabul et
                            ->directory('assets') // storage/app/public/assets klasorune kaydet
                            ->label('Ürün Görseli'),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Ürün Adı (Örn: MacBook M1)'),

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
                            ->unique(ignoreRecord: true)
                            ->label('Seri Numarası'),
                    ])->columns(2),

                Forms\Components\Section::make('Zimmet Durumu')
                    ->schema([
                        // relation secimi (Select Menü)
                        // relationship('employee', 'first_name'): Employee tablosuna git first_name'i goster
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->searchable()
                            ->preload()
                            ->label('Zimmetli Kişi'),

                        Forms\Components\Toggle::make('is_available')
                            ->label('Kullanıma Uygun')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // mini image gosteriyorum
                Tables\Columns\ImageColumn::make('image')
                    ->label('Görsel'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Ürün Adı'),

                Tables\Columns\TextColumn::make('type')
                    ->badge() // renkli etiket
                    ->label('Tip'),

                // İlişkili veriyi gösterme (Çalışanın Adı)
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Zimmetli Kişi')
                    ->placeholder('Depoda') // eger kimse yoksa 'Depoda' yazar
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->label('Durum'),
            ])
            ->filters([
                // filter ekliyoruz (ornegin sadece laptoplar)
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'laptop' => 'Laptop',
                        'monitor' => 'Monitör',
                    ]),
            ])
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
