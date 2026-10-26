<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Proje Detayları')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->label('Proje Adı'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Bekliyor',
                                'in_progress' => 'Devam Ediyor',
                                'completed' => 'Tamamlandı',
                            ])
                            ->default('pending')
                            ->label('Durum'),

                        Forms\Components\DatePicker::make('deadline')
                            ->label('Teslim Tarihi'),

                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(), // tam genislik sagliyor
                    ])->columns(2),

                Forms\Components\Section::make('Proje Ekibi')
                    ->schema([
                        //many-to-many kısmı burasi
                        Forms\Components\Select::make('employees')
                            ->relationship('employees', 'first_name') // relation adı ve gosterilecek column
                            ->multiple() // birden cok kisi secilebilsin (many to many olabiliyor boylece)
                            ->preload() // listi upload eder
                            ->label('Takım Arkadaşları'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Proje'),

                // takimdaki kisilerin isimlerini avatar stack olarak gosterdim
                Tables\Columns\TextColumn::make('employees.first_name')
                    ->badge()
                    ->label('Ekip'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                    }),

                Tables\Columns\TextColumn::make('deadline')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
