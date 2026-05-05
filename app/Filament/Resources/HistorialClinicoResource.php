<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorialClinicoResource\Pages;
use App\Models\HistorialClinico;
use App\Models\Cita;
use App\Models\Material;
use App\Models\Servicio;
use App\Models\Ejercicio;
use App\Models\Paciente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class HistorialClinicoResource extends Resource
{
    protected static ?string $model = HistorialClinico::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = '📋 Clínica';
    protected static ?int $navigationSort = 12;
    protected static ?string $recordTitleAttribute = 'evaluacion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📅 Progreso de Sesiones')
                    ->description('Control de avance del tratamiento')
                    ->schema([
                        Forms\Components\TextInput::make('cita.sesion_actual')
                            ->label('Sesión Actual')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->default(fn ($record) => $record?->cita?->sesion_actual ?? 1)
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('cita.sesiones_totales')
                            ->label('Total de Sesiones Planificadas')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->default(fn ($record) => $record?->cita?->sesiones_totales ?? 1)
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('sesiones_restantes')
                            ->label('Sesiones Restantes')
                            ->content(fn (callable $get) => max(0, ($get('cita.sesiones_totales') ?? 1) - ($get('cita.sesion_actual') ?? 1)))
                            ->columnSpan(1),
                    ])->columns(3),

                Forms\Components\Section::make('Información de la Consulta')
                    ->schema([
                        Forms\Components\Select::make('citas_id')
                            ->label('Cita Realizada')
                            ->relationship(
                                'cita', 
                                'motivo',
                                fn(Builder $query) => $query->where('estado', 'realizado')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn($record) => $record !== null)
                            ->getOptionLabelFromRecordUsing(fn(Cita $record): string => 
                                "{$record->paciente->nombre} - " . \Carbon\Carbon::parse($record->fecha_hora)->format('d/m/Y H:i')
                            )
                            ->getSearchResultsUsing(function (string $search): array {
                                return Cita::where('estado', 'realizado')
                                    ->with('paciente')
                                    ->whereHas('paciente', function ($query) use ($search) {
                                        $query->where('nombre', 'like', "%{$search}%")
                                            ->orWhere('ci', 'like', "%{$search}%");
                                    })
                                    ->orWhere('motivo', 'like', "%{$search}%")
                                    ->get()
                                    ->mapWithKeys(fn(Cita $cita) => [
                                        $cita->id => "{$cita->paciente->nombre} - " . \Carbon\Carbon::parse($cita->fecha_hora)->format('d/m/Y H:i')
                                    ])
                                    ->toArray();
                            })
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('codigo_acceso')
                            ->label('Código de Acceso para el Paciente')
                            ->default(fn() => strtoupper(Str::random(8)))
                            ->readOnly()
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('copiar')
                                    ->icon('heroicon-o-clipboard-document')
                                    ->label('Copiar')
                                    ->action(function ($state, $livewire) {
                                        $livewire->js(<<<JS
                                            navigator.clipboard.writeText('{$state}');
                                            Filament.Notification.make()
                                                .title('Código copiado')
                                                .success()
                                                .duration(2000)
                                                .send();
                                        JS);
                                    })
                            )
                            ->columnSpan(1),
                    ])->columns(2),

                Forms\Components\Section::make('Evaluación Clínica')
                    ->schema([
                        Forms\Components\Textarea::make('evaluacion')
                            ->label('Evaluación del Profesional')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('recomendaciones')
                            ->label('Recomendaciones para el Paciente')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('recetas_compra')
                            ->label('Recetas / Materiales a Adquirir')
                            ->nullable()
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Materiales Utilizados en la Sesión')
                    ->schema([
                        Forms\Components\Repeater::make('materiales_usados')
                            ->relationship('materialesUsados')
                            ->schema([
                                Forms\Components\Select::make('materiales_id')
                                    ->label('Material')
                                    ->options(fn() => Material::pluck('nombre', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $material = Material::find($state);
                                            $set('stock_disponible', $material?->stock_actual ?? 0);
                                        }
                                    })
                                    ->columnSpan(2),
                                
                                Forms\Components\TextInput::make('stock_disponible')
                                    ->label('Stock Disponible')
                                    ->numeric()
                                    ->readOnly()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(1),
                                
                                Forms\Components\TextInput::make('cantidad_usada')
                                    ->label('Cantidad Utilizada')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->addActionLabel('Agregar Material')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Servicios Prestados')
                    ->schema([
                        Forms\Components\Repeater::make('servicios')
                            ->relationship('servicios')
                            ->schema([
                                Forms\Components\Select::make('nombre_servicio')
                                    ->label('Servicio')
                                    ->options(fn() => Servicio::pluck('nombre_servicio', 'nombre_servicio'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (string $state, callable $set) {
                                        $servicio = Servicio::where('nombre_servicio', $state)->first();
                                        
                                        if ($servicio) {
                                            $set('precio', $servicio->precio);
                                            $set('duracion_minutos', $servicio->duracion_minutos);
                                            $set('sesiones_aplicadas', $servicio->sesiones_recomendadas ?? 1);
                                        }
                                    })
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre_servicio')
                                            ->label('Nombre del Servicio')
                                            ->required()
                                            ->maxLength(255),
                                        
                                        Forms\Components\TextInput::make('precio')
                                            ->label('Precio (Bs)')
                                            ->numeric()
                                            ->prefix('Bs')
                                            ->minValue(0),
                                        
                                        Forms\Components\TextInput::make('duracion_minutos')
                                            ->label('Duración (min)')
                                            ->numeric()
                                            ->minValue(5),
                                        
                                        Forms\Components\TextInput::make('sesiones_recomendadas')
                                            ->label('Sesiones Recomendadas')
                                            ->numeric()
                                            ->default(1),
                                    ])
                                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->modalHeading('Crear Nuevo Servicio')
                                            ->modalSubmitActionLabel('Crear Servicio')
                                            ->modalWidth('lg');
                                    })
                                    ->required()
                                    ->columnSpan(2),
                                
                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio (Bs)')
                                    ->numeric()
                                    ->prefix('Bs')
                                    ->minValue(0)
                                    ->required()
                                    ->columnSpan(1),
                                
                                Forms\Components\TextInput::make('duracion_minutos')
                                    ->label('Duración (min)')
                                    ->numeric()
                                    ->minValue(5)
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('sesiones_aplicadas')
                                    ->label('Sesiones Realizadas')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->addActionLabel('Agregar Servicio')
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('🏋️ Ejercicios Recomendados')
                    ->description('Ejercicios sugeridos para esta consulta')
                    ->schema([
                        Forms\Components\Repeater::make('ejerciciosRecomendados')
                            ->relationship('ejerciciosRecomendados')
                            ->schema([
                                Forms\Components\Select::make('ejercicios_id')
                                    ->label('Ejercicio')
                                    ->relationship('ejercicio', 'nombre_ejercicio')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre_ejercicio')
                                            ->label('Nombre del Ejercicio')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\Textarea::make('descripcion')
                                            ->label('Descripción')
                                            ->nullable()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\TextInput::make('video_url')
                                            ->label('URL del Video (opcional)')
                                            ->url()
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ])
                                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->modalHeading('Crear Nuevo Ejercicio')
                                            ->modalSubmitActionLabel('Crear Ejercicio')
                                            ->modalWidth('lg');
                                    }),
                                
                                Forms\Components\TextInput::make('series')
                                    ->label('Series')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(3)
                                    ->required()
                                    ->columnSpan(1),
                                
                                Forms\Components\TextInput::make('repeticiones')
                                    ->label('Repeticiones')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(10)
                                    ->required()
                                    ->columnSpan(1),
                                
                                Forms\Components\TextInput::make('frecuencia')
                                    ->label('Frecuencia')
                                    ->placeholder('Ej: 3 veces por semana')
                                    ->required()
                                    ->columnSpan(2),
                                
                                Forms\Components\DatePicker::make('fecha_asignacion')
                                    ->label('Fecha de Asignación')
                                    ->default(now())
                                    ->required()
                                    ->columnSpan(1),
                                
                                Forms\Components\Textarea::make('notas_adicionales')
                                    ->label('Notas para el paciente')
                                    ->nullable()
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->addActionLabel('➕ Agregar Ejercicio')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cita.paciente.nombre')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('cita.fecha_hora')
                    ->label('Fecha Consulta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cita.sesion_actual')
                    ->label('Progreso')
                    ->formatStateUsing(fn ($record): string => "{$record->cita->sesion_actual}/{$record->cita->sesiones_totales}")
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('evaluacion')
                    ->label('Evaluación')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->evaluacion),

                Tables\Columns\TextColumn::make('codigo_acceso')
                    ->label('Código Acceso')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Código copiado'),

                Tables\Columns\TextColumn::make('materialesUsados_count')
                    ->label('Materiales')
                    ->counts('materialesUsados')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('servicios_count')
                    ->label('Servicios')
                    ->counts('servicios')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('paciente')
                    ->label('Por Paciente')
                    ->relationship('cita.paciente', 'nombre')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('fecha_consulta')
                    ->form([
                        Forms\Components\DatePicker::make('desde'),
                        Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn($q, $d) => $q->whereHas('cita', fn($c) => $c->whereDate('fecha_hora', '>=', $d)))
                            ->when($data['hasta'] ?? null, fn($q, $d) => $q->whereHas('cita', fn($c) => $c->whereDate('fecha_hora', '<=', $d)));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('enlace_publico')
                    ->label('Enlace Público')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->url(fn(HistorialClinico $record): string => route('consulta.resultado', $record->codigo_acceso))
                    ->openUrlInNewTab()
                    ->visible(fn(HistorialClinico $record) => !empty($record->codigo_acceso)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['cita.paciente'])->withCount(['materialesUsados', 'servicios']));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Consulta')
                    ->schema([
                        TextEntry::make('cita.paciente.nombre')->label('Paciente'),
                        TextEntry::make('cita.fecha_hora')->label('Fecha y Hora')->dateTime('d/m/Y H:i'),
                        TextEntry::make('codigo_acceso')
                            ->label('Código de Acceso')
                            ->badge()
                            ->color('primary')
                            ->copyable(),
                        TextEntry::make('cita.sesion_actual')
                            ->label('Progreso')
                            ->formatStateUsing(fn ($record): string => "{$record->cita->sesion_actual}/{$record->cita->sesiones_totales}")
                            ->badge()
                            ->color('info'),
                    ])->columns(4),

                Section::make('Evaluación Clínica')
                    ->schema([
                        TextEntry::make('evaluacion')->label('Evaluación')->columnSpanFull(),
                        TextEntry::make('recomendaciones')->label('Recomendaciones')->columnSpanFull(),
                        TextEntry::make('recetas_compra')->label('Recetas / Materiales a Comprar')->columnSpanFull(),
                    ]),

                Section::make('Materiales Utilizados')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('materialesUsados')
                            ->schema([
                                TextEntry::make('material.nombre')->label('Material'),
                                TextEntry::make('cantidad_usada')->label('Cantidad'),
                            ])->columns(2),
                    ]),

                Section::make('Servicios Prestados')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('servicios')
                            ->schema([
                                TextEntry::make('nombre_servicio')->label('Servicio'),
                                TextEntry::make('precio')->label('Precio')->money('BOB'),
                                TextEntry::make('duracion_minutos')->label('Duración')->suffix(' min'),
                            ])->columns(3),
                    ]),

                Section::make('Ejercicios Asignados')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('cita.paciente.ejercicios')
                            ->schema([
                                TextEntry::make('ejercicio.nombre_ejercicio')->label('Ejercicio'),
                                TextEntry::make('pivot.series')->label('Series'),
                                TextEntry::make('pivot.repeticiones')->label('Repeticiones'),
                                TextEntry::make('pivot.frecuencia')->label('Frecuencia'),
                                TextEntry::make('pivot.fecha_asignacion')->label('Asignado')->date('d/m/Y'),
                            ])->columns(3),
                    ]),
                    
                Section::make('Ejercicios Recomendados')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('ejerciciosRecomendados')
                            ->schema([
                                TextEntry::make('ejercicio.nombre_ejercicio')
                                    ->label('Ejercicio')
                                    ->icon('heroicon-o-play-circle'),
                                TextEntry::make('series')
                                    ->label('Series')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('repeticiones')
                                    ->label('Repeticiones')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('frecuencia')
                                    ->label('Frecuencia'),
                                TextEntry::make('fecha_asignacion')
                                    ->label('Asignado')
                                    ->date('d/m/Y'),
                                TextEntry::make('notas_adicionales')
                                    ->label('Notas')
                                    ->columnSpanFull(),
                            ])->columns(3),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getRecordTitle($record): string
    {
        return $record->cita->paciente->nombre ?? 'Sin paciente';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistorialClinicos::route('/'),
            'create' => Pages\CreateHistorialClinico::route('/create'),
            'view' => Pages\ViewHistorialClinico::route('/{record}'),
            'edit' => Pages\EditHistorialClinico::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return 'Historial Clínico';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Historiales Clínicos';
    }

    public static function getNavigationLabel(): string
    {
        return 'Historiales Clínicos';
    }
}