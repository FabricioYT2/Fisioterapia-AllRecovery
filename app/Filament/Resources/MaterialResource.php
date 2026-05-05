<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = '📦 Inventario';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Material')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre del Material')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('unidad')
                            ->label('Unidad de Medida')
                            ->required()
                            ->placeholder('Ej: unidades, cajas, litros')
                            ->maxLength(50)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('stock_actual')
                            ->label('Stock Actual')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('stock_minimo')
                            ->label('Stock Mínimo')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(5)
                            ->helperText('Se alertará cuando el stock llegue a este nivel')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Material')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-archive-box'),
                
                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (int $state): string => match (true) {
                        $state === 0 => '🔴 Agotado',
                        $state <= 5 => '⚠️ Bajo',
                        default => '✅ ' . $state,
                    }),
                
                Tables\Columns\TextColumn::make('stock_minimo')
                    ->label('Mínimo')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('unidad')
                    ->label('Unidad')
                    ->badge()
                    ->color('gray'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Estado del Stock')
                    ->options([
                        'agotado' => '🔴 Agotados',
                        'bajo' => '⚠️ Stock Bajo',
                        'normal' => '✅ Stock Normal',
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['value'], function ($q, $status) {
                            return match ($status) {
                                'agotado' => $q->where('stock_actual', 0),
                                'bajo' => $q->whereColumn('stock_actual', '<=', 'stock_minimo')->where('stock_actual', '>', 0),
                                'normal' => $q->whereColumn('stock_actual', '>', 'stock_minimo'),
                                default => $q,
                            };
                        });
                    }),
            ])
            ->headerActions([
                Action::make('exportar_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('primary')
                    ->action(function () {
                        $materiales = Material::orderBy('nombre')->get();
                        $stock_bajo = $materiales->filter(fn($m) => $m->stock_actual <= $m->stock_minimo)->count();
                        $agotados = $materiales->filter(fn($m) => $m->stock_actual == 0)->count();
                        
                        $pdf = Pdf::loadView('reports.pdf.inventario', [
                            'materiales' => $materiales,
                            'stock_bajo' => $stock_bajo,
                            'agotados' => $agotados,
                            'titulo' => 'Inventario de Materiales',
                            'fecha_generacion' => now()->format('d/m/Y H:i'),
                        ])->setPaper('a4');
                        
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'inventario_' . now()->format('Y-m-d') . '.pdf'
                        );
                    }),
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
            ->defaultSort('nombre', 'asc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información del Material')
                    ->schema([
                        TextEntry::make('nombre')
                            ->label('Nombre del Material'),
                        TextEntry::make('stock_actual')
                            ->label('Stock Actual')
                            ->badge()
                            ->color(fn (int $state): string => match (true) {
                                $state === 0 => 'danger',
                                $state <= 5 => 'warning',
                                default => 'success',
                            }),
                        TextEntry::make('stock_minimo')
                            ->label('Stock Mínimo'),
                        TextEntry::make('unidad')
                            ->label('Unidad de Medida'),
                        TextEntry::make('created_at')
                            ->label('Registrado')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Actualizado')
                            ->dateTime('d/m/Y H:i'),
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
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'view' => Pages\ViewMaterial::route('/{record}'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'Material';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Materiales';
    }

    public static function getNavigationLabel(): string
    {
        return 'Materiales';
    }
}