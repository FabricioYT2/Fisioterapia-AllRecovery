<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CitaResource\Pages;
use App\Models\Cita;
use App\Models\Paciente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CitaResource extends Resource
{
    protected static ?string $model = Cita::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = '📅 Agenda';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos de la Cita')
                    ->schema([
                        Forms\Components\Select::make('pacientes_id')
                            ->label('Paciente')
                            ->relationship('paciente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\DatePicker::make('fecha_hora')
                            ->label('Fecha')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->minDate(now())
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\Select::make('turno')
                            ->label('Turno')
                            ->options([
                                'manana' => '🌅 Mañana (8:00 - 12:00)',
                                'tarde' => '🌆 Tarde (14:00 - 18:00)',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $fecha = $get('fecha_hora');
                                if ($fecha && $state) {
                                    $cupos = \App\Models\TurnoConfig::getCuposDisponibles($fecha, $state);
                                    if ($cupos <= 0) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Sin cupos disponibles')
                                            ->body('Este turno ya está completo para la fecha seleccionada.')
                                            ->danger()
                                            ->send();
                                    }
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'pendiente' => '⏳ Pendiente',
                                'realizado' => '✅ Realizado',
                                'cancelado' => '❌ Cancelado',
                            ])
                            ->default('pendiente')
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Textarea::make('motivo')
                            ->label('Motivo de Consulta')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
                     
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paciente.nombre')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('fecha_hora')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'realizado',
                        'danger' => 'cancelado',
                    ])
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'pendiente' => '⏳ Pendiente',
                        'realizado' => '✅ Realizado',
                        'cancelado' => '❌ Cancelado',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('fuente')
                    ->label('Origen')
                    ->badge()
                    ->colors([
                        'primary' => 'web',
                        'gray' => 'admin',
                    ])
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'web' => '🌐 Web',
                        'admin' => '👨‍💻 Admin',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('motivo')
                    ->label('Motivo')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->motivo),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Por Estado')
                    ->options([
                        'pendiente' => '⏳ Pendientes',
                        'realizado' => '✅ Realizadas',
                        'cancelado' => '❌ Canceladas',
                    ]),

                Tables\Filters\SelectFilter::make('fuente')
                    ->label('Origen')
                    ->options([
                        'web' => '🌐 Formulario Web',
                        'admin' => '👨‍💻 Panel Admin',
                    ]),

                Tables\Filters\Filter::make('fecha_hora')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $d) => $q->whereDate('fecha_hora', '>=', $d))
                            ->when($data['hasta'] ?? null, fn ($q, $d) => $q->whereDate('fecha_hora', '<=', $d));
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
                        $estado = request('estado') ?? 'todos';
                        $query = Cita::with('paciente');
                        if ($estado !== 'todos') {
                            $query->where('estado', $estado);
                        }
                        $query->whereBetween('fecha_hora', [$desde, $hasta]);
                        $citas = $query->orderBy('fecha_hora')->get();
                        
                        $estadisticas = [
                            'total' => Cita::whereBetween('fecha_hora', [$desde, $hasta])->count(),
                            'pendiente' => Cita::where('estado', 'pendiente')->whereBetween('fecha_hora', [$desde, $hasta])->count(),
                            'realizado' => Cita::where('estado', 'realizado')->whereBetween('fecha_hora', [$desde, $hasta])->count(),
                            'cancelado' => Cita::where('estado', 'cancelado')->whereBetween('fecha_hora', [$desde, $hasta])->count(),
                        ];

                        $ingresos = \App\Models\IngresoEgreso::where('tipo', 'ingreso')
                            ->whereBetween('fecha', [$desde, $hasta])
                            ->orderBy('fecha')
                            ->get();

                        $egresos = \App\Models\IngresoEgreso::where('tipo', 'egreso')
                            ->whereBetween('fecha', [$desde, $hasta])
                            ->orderBy('fecha')
                            ->get();

                        $total_ingresos = $ingresos->sum('monto');
                        $total_egresos = $egresos->sum('monto');
                        $balance = $total_ingresos - $total_egresos;

                        $pdf = Pdf::loadView('reports.pdf.citas', [
                            'citas' => $citas,
                            'estadisticas' => $estadisticas,
                            'titulo' => 'Reporte de Citas',
                            'fecha_generacion' => now()->format('d/m/Y H:i'),
                            'periodo' => 'Del ' . Carbon::parse($desde)->format('d/m/Y') . ' al ' . Carbon::parse($hasta)->format('d/m/Y'),
                            'ingresos' => $ingresos,
                            'egresos' => $egresos,
                            'total_ingresos' => $total_ingresos,
                            'total_egresos' => $total_egresos,
                            'balance' => $balance,
                        ])->setPaper('a4');
                        
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'citas_' . now()->format('Y-m-d') . '.pdf'
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
 
                Tables\Actions\Action::make('completar')
                    ->label('Completar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(Cita $record) => $record->update(['estado' => 'realizado']))
                    ->visible(fn(Cita $record) => $record->estado === 'pendiente'),

                Tables\Actions\DeleteAction::make()
                    ->before(fn(Cita $r) => throw_if($r->cobros()->exists(), new \Exception('No se puede eliminar: tiene cobros asociados.'))),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_hora', 'desc')
            ->defaultPaginationPageOption(15) 
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['paciente'])); 
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detalle de la Cita')
                    ->schema([
                        TextEntry::make('paciente.nombre')
                            ->label('Paciente'),
                        TextEntry::make('paciente.ci')
                            ->label('C.I.'),
                        TextEntry::make('paciente.telefono')
                            ->label('Teléfono')
                            ->copyable(),
                        TextEntry::make('paciente.email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('fecha_hora')
                            ->label('Fecha y Hora')
                            ->dateTime('d/m/Y H:i'),
                        
                        TextEntry::make('estado')
                            ->label('Estado')
                            ->badge()
                            ->colors([
                                'warning' => 'pendiente',
                                'success' => 'realizado',
                                'danger' => 'cancelado',
                            ])
                            ->formatStateUsing(fn ($state): string => match($state) {
                                'pendiente' => '⏳ Pendiente',
                                'realizado' => '✅ Realizado',
                                'cancelado' => '❌ Cancelado',
                                default => $state ?? 'Sin definir',
                            }),
                        
                        TextEntry::make('fuente')
                            ->label('Origen')
                            ->badge()
                            ->colors([
                                'primary' => 'web',
                                'gray' => 'admin',
                            ])
                            ->formatStateUsing(fn ($state): string => match($state) {
                                'web' => '🌐 Formulario Web',
                                'admin' => '👨‍💻 Panel Admin',
                                default => $state ?? 'Desconocido',
                            }),
                        
                        TextEntry::make('turno')
                            ->label('Turno')
                            ->badge()
                            ->formatStateUsing(fn ($state): string => match($state) {
                                'manana' => '🌅 Mañana',
                                'tarde' => '🌆 Tarde',
                                default => $state ?? 'Sin asignar',
                            }),
                        
                        TextEntry::make('motivo')
                            ->label('Motivo')
                            ->columnSpanFull(),
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
            'index' => Pages\ListCitas::route('/'),
            'create' => Pages\CreateCita::route('/create'),
            'view' => Pages\ViewCita::route('/{record}'),
            'edit' => Pages\EditCita::route('/{record}/edit'),
        ];
    }

    public static function canDelete($record): bool
    {
        return !$record->cobros()->exists();
    }

    public static function getLabel(): ?string
    {
        return 'Cita';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Citas';
    }

    public static function getNavigationLabel(): string
    {
        return 'Citas';
    }
}