<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompraMaterialResource\Pages;
use App\Models\CompraMaterial;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Notifications\Notification;

class CompraMaterialResource extends Resource
{
    protected static ?string $model = CompraMaterial::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = '📦 Inventario';
    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Compra')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_compra')
                            ->label('Fecha de Compra')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('costo_total')
                            ->label('Costo Total (Bs)')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('Bs')
                            ->readOnly()
                            ->dehydrated()
                            ->columnSpan(1),
                    ])->columns(2),

                Forms\Components\Section::make('Materiales Comprados')
                    ->schema([
                        Forms\Components\Repeater::make('detalleCompraMateriales')
                            ->relationship('detalleCompraMateriales')
                            ->schema([
                                Forms\Components\Select::make('materiales_id')
                                    ->label('Material')
                                    ->options(fn() => Material::pluck('nombre', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->live()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('precio_unitario')
                                    ->label('Precio Unitario (Bs)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->prefix('Bs')
                                    ->live()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Bs')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->addActionLabel('➕ Agregar Material')
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $total = 0;
                                
                                if (is_array($state)) {
                                    $updated = [];
                                    
                                    foreach ($state as $index => $item) {
                                        $cantidad = floatval($item['cantidad'] ?? 0);
                                        $precio = floatval($item['precio_unitario'] ?? 0);
                                        
                                        $subtotal = $cantidad * $precio;
                                        $item['subtotal'] = $subtotal;
                                        $total += $subtotal;
                                        
                                        $updated[$index] = $item;
                                    }
                                    
                                    $set('detalleCompraMateriales', $updated);
                                }
                                
                                $set('costo_total', floatval($total));
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_compra')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('detalleCompraMateriales.material.nombre')
                    ->label('Materiales')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->detalleCompraMateriales->pluck('material.nombre')->join(', '))
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('costo_total')
                    ->label('Costo Total')
                    ->money('BOB', true)
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('fecha_compra', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información de la Compra')
                    ->schema([
                        TextEntry::make('fecha_compra')->label('Fecha')->date('d/m/Y'),
                        TextEntry::make('costo_total')->label('Costo Total')->money('BOB'),
                    ])->columns(2),

                Section::make('Detalle de Materiales')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('detalleCompraMateriales')
                            ->schema([
                                TextEntry::make('material.nombre')->label('Material'),
                                TextEntry::make('cantidad')->label('Cantidad'),
                                TextEntry::make('precio_unitario')->label('Precio Unitario')->money('BOB'),
                                TextEntry::make('subtotal')->label('Subtotal')->money('BOB'),
                            ])->columns(4),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompraMateriales::route('/'),
            'create' => Pages\CreateCompraMaterial::route('/create'),
            'view' => Pages\ViewCompraMaterial::route('/{record}'),
            'edit' => Pages\EditCompraMaterial::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string { return 'Nueva Adquisición'; }
    public static function getPluralLabel(): ?string { return 'Reabastecimiento de Inventario'; }
    public static function getNavigationLabel(): string { return 'Reabastecimiento'; }
    public static function procesarCompra(CompraMaterial $compra): void
{
    $compra->load('detalleCompraMateriales');
    
    foreach ($compra->detalleCompraMateriales as $detalle) {
        $material = Material::find($detalle->materiales_id);
        if ($material) {
            $material->increment('stock_actual', (int) $detalle->cantidad);
        }
    }
    
    $exists = IngresoEgreso::where('compra_materiales_id', $compra->id)->exists();
    if (!$exists) {
        IngresoEgreso::create([
            'compra_materiales_id' => $compra->id,
            'tipo' => 'egreso',
            'monto' => (float) $compra->costo_total,
            'fecha' => $compra->fecha_compra ?? now(),
            'descripcion' => 'Compra de materiales (Compra #' . $compra->id . ')',
        ]);
    }
}
}