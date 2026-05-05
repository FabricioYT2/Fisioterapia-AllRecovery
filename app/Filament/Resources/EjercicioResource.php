<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EjercicioResource\Pages;
use App\Models\Ejercicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class EjercicioResource extends Resource
{
    protected static ?string $model = Ejercicio::class;
    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationGroup = '📋 Clínica';
    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Ejercicio')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_ejercicio')
                            ->label('Nombre del Ejercicio')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('video_url')
                            ->label('URL del Video')
                            ->required()
                            ->url()
                            ->placeholder('https://youtube.com/watch?v=...')
                            ->helperText('Enlace a YouTube o video demostrativo')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_ejercicio')
                    ->label('Ejercicio')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-play-circle'),
                
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('video_url')
                    ->label('Video')
                    ->url(fn (string $state): string => $state)
                    ->openUrlInNewTab()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([

            ])
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
            ->defaultSort('nombre_ejercicio', 'asc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información del Ejercicio')
                    ->schema([
                        TextEntry::make('nombre_ejercicio')
                            ->label('Nombre del Ejercicio'),
                        TextEntry::make('descripcion')
                            ->label('Descripción')
                            ->columnSpanFull(),
                        TextEntry::make('video_url')
                            ->label('URL del Video')
                            ->url(fn (string $state): string => $state)
                            ->openUrlInNewTab()
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Actualizado')
                            ->dateTime('d/m/Y H:i'),
                    ])->columns(2),
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
            'index' => Pages\ListEjercicios::route('/'),
            'create' => Pages\CreateEjercicio::route('/create'),
            'view' => Pages\ViewEjercicio::route('/{record}'),
            'edit' => Pages\EditEjercicio::route('/{record}/edit'),
        ];
    }
    public static function getLabel(): ?string
    {
        return 'Ejercicio';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Ejercicios';
    }

    public static function getNavigationLabel(): string
    {
        return 'Ejercicios';
    }
}