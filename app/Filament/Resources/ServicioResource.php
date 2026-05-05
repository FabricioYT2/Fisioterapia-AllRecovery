<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioResource\Pages;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = '📋 Clínica';
    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Servicio')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_servicio')
                            ->label('Nombre del Servicio')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('precio')
                            ->label('Precio (Bs)')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('Bs')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('duracion_minutos')
                            ->label('Duración (minutos)')
                            ->required()
                            ->numeric()
                            ->minValue(5)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('sesiones_recomendadas')
                        ->label('Sesiones Recomendadas')
                            ->helperText('Cantidad estimada de sesiones para completar el tratamiento')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->columnSpan(1),
                        Forms\Components\Hidden::make('historial_clinicos_id'),                           
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_servicio')
                    ->label('Servicio')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-wrench-screwdriver'),

                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->money('BOB', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('duracion_minutos')
                    ->label('Duración')
                    ->sortable()
                    ->suffix(' min'),
                Tables\Columns\TextColumn::make('sesiones_recomendadas')
                    ->label('Sesiones Est.')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-clock')
                    ->default('1'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nombre_servicio', 'asc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detalles del Servicio')
                    ->schema([
                        TextEntry::make('nombre_servicio')->label('Servicio'),
                        TextEntry::make('precio')->label('Precio')->money('BOB'),
                        TextEntry::make('duracion_minutos')->label('Duración')->suffix(' minutos'),
                        TextEntry::make('created_at')->label('Creado')->dateTime('d/m/Y H:i'),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServicios::route('/'),
            'create' => Pages\CreateServicio::route('/create'),
            'view' => Pages\ViewServicio::route('/{record}'),
            'edit' => Pages\EditServicio::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string { return 'Servicio'; }
    public static function getPluralLabel(): ?string { return 'Servicios'; }
    public static function getNavigationLabel(): string { return 'Servicios'; }
}