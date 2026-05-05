<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngresoEgresoResource\Pages;
use App\Models\IngresoEgreso;
use App\Models\Cobro;
use App\Models\CompraMaterial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;

class IngresoEgresoResource extends Resource
{
    protected static ?string $model = IngresoEgreso::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = '💰 Finanzas';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Movimiento')
                    ->schema([
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de Movimiento')
                            ->options([
                                'ingreso' => '📈 Ingreso',
                                'egreso' => '📉 Egreso',
                            ])
                            ->required()
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('monto')
                            ->label('Monto (Bs)')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('Bs')
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('cobros_id')
                            ->label('Cobro Relacionado')
                            ->options(Cobro::pluck('id', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn (Forms\Get $get) => $get('tipo') === 'ingreso')
                            ->columnSpan(1),

                        Forms\Components\Select::make('compra_materiales_id')
                            ->label('Compra Relacionada')
                            ->options(CompraMaterial::pluck('id', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn (Forms\Get $get) => $get('tipo') === 'egreso')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->colors([
                        'success' => 'ingreso',
                        'danger' => 'egreso',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'ingreso' => '📈 Ingreso',
                        'egreso' => '📉 Egreso',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('BOB', true)
                    ->sortable()
                    ->color(fn (string $state, IngresoEgreso $record): string => 
                        $record->tipo === 'ingreso' ? 'success' : 'danger'
                    ),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->descripcion),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Por Tipo')
                    ->options([
                        'ingreso' => 'Ingresos',
                        'egreso' => 'Egresos',
                    ]),

                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $d) => $q->whereDate('fecha', '>=', $d))
                            ->when($data['hasta'] ?? null, fn ($q, $d) => $q->whereDate('fecha', '<=', $d));
                    }),
            ])
            ->headerActions([
                Action::make('exportar_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('primary')
                    ->action(function () {
                        $desde = request('desde') ?? now()->startOfMonth();
                        $hasta = request('hasta') ?? now()->endOfMonth();
                        
                        $ingresos = IngresoEgreso::where('tipo', 'ingreso')->whereBetween('fecha', [$desde, $hasta])->get();
                        $egresos = IngresoEgreso::where('tipo', 'egreso')->whereBetween('fecha', [$desde, $hasta])->get();
                        
                        $pdf = Pdf::loadView('reports.pdf.financiero', [
                            'ingresos' => $ingresos,
                            'egresos' => $egresos,
                            'total_ingresos' => $ingresos->sum('monto'),
                            'total_egresos' => $egresos->sum('monto'),
                            'balance' => $ingresos->sum('monto') - $egresos->sum('monto'),
                            'titulo' => 'Reporte Financiero',
                            'fecha_generacion' => now()->format('d/m/Y H:i'),
                            'periodo' => 'Del ' . \Carbon\Carbon::parse($desde)->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($hasta)->format('d/m/Y'),
                        ])->setPaper('a4');
                        
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'financiero_' . now()->format('Y-m-d') . '.pdf'
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
            ->defaultSort('fecha', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detalles del Movimiento')
                    ->schema([
                        TextEntry::make('tipo')
                            ->label('Tipo')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match($state) {
                                'ingreso' => '📈 Ingreso',
                                'egreso' => '📉 Egreso',
                                default => $state,
                            }),
                        TextEntry::make('monto')
                            ->label('Monto')
                            ->money('BOB')
                            ->color(fn (IngresoEgreso $record): string => 
                                $record->tipo === 'ingreso' ? 'success' : 'danger'
                            ),
                        TextEntry::make('fecha')
                            ->label('Fecha')
                            ->date('d/m/Y'),
                        TextEntry::make('descripcion')
                            ->label('Descripción')
                            ->columnSpanFull(),
                        TextEntry::make('cobros_id')
                            ->label('Cobro Relacionado')
                            ->placeholder('No aplica'),
                        TextEntry::make('compra_materiales_id')
                            ->label('Compra Relacionada')
                            ->placeholder('No aplica'),
                        TextEntry::make('created_at')
                            ->label('Registrado')
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
            'index' => Pages\ListIngresosEgresos::route('/'),
            'create' => Pages\CreateIngresoEgreso::route('/create'),
            'view' => Pages\ViewIngresoEgreso::route('/{record}'),
            'edit' => Pages\EditIngresoEgreso::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'Ingreso/Egreso';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Ingresos y Egresos';
    }

    public static function getNavigationLabel(): string
    {
        return 'Ingresos y Egresos';
    }
}