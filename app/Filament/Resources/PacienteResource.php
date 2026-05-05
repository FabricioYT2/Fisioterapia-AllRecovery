<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PacienteResource\Pages;
use App\Models\Paciente;
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

class PacienteResource extends Resource
{
    protected static ?string $model = Paciente::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = '👥 Pacientes';
    protected static ?string $navigationLabel = 'Pacientes';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos Personales')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('ci')
                            ->label('Cédula de Identidad')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('edad')
                            ->label('Edad')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(120)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->required()
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        Forms\Components\Select::make('actividad_fisica')
                            ->label('Actividad Física')
                            ->required()
                            ->options([
                                'competencia' => '🏆 Competencia Deportiva',
                                'salud' => '💚 Salud / Bienestar',
                                'ninguna' => '⚪ Ninguna',
                            ])
                            ->default('ninguna')
                            ->columnSpan(1),
                        
                        Forms\Components\DatePicker::make('fecha_registro')
                            ->label('Fecha de Registro')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),
                
                Tables\Columns\TextColumn::make('ci')
                    ->label('C.I.')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                
                Tables\Columns\TextColumn::make('edad')
                    ->label('Edad')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('actividad_fisica')
                    ->label('Actividad')
                    ->badge()
                    ->colors([
                        'success' => 'competencia',
                        'primary' => 'salud',
                        'gray' => 'ninguna',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'competencia' => '🏆 Competencia',
                        'salud' => '💚 Salud',
                        'ninguna' => '⚪ Ninguna',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('fecha_registro')
                    ->label('Registro')
                    ->date('d/m/Y')
                    ->sortable(),
                
                // ✅ Optimizado: Se carga junto con la lista principal
                Tables\Columns\TextColumn::make('citas_count')
                    ->label('Citas')
                    ->counts('citas')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('actividad_fisica')
                    ->options([
                        'competencia' => 'Competencia',
                        'salud' => 'Salud',
                        'ninguna' => 'Ninguna',
                    ]),
                
                Tables\Filters\Filter::make('fecha_registro')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_desde'),
                        Forms\Components\DatePicker::make('fecha_hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['fecha_desde'] ?? null, fn ($q, $d) => $q->whereDate('fecha_registro', '>=', $d))
                            ->when($data['fecha_hasta'] ?? null, fn ($q, $d) => $q->whereDate('fecha_registro', '<=', $d));
                    }),
            ])
            ->headerActions([
                Action::make('exportar_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('primary')
                    ->action(function () {
                        $pacientes = Paciente::withCount('citas')->orderBy('nombre')->get();
                        $pdf = Pdf::loadView('reports.pdf.pacientes', [
                            'pacientes' => $pacientes,
                            'titulo' => 'Lista de Pacientes',
                            'fecha_generacion' => now()->format('d/m/Y H:i'),
                        ])->setPaper('a4');
                        
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'pacientes_' . now()->format('Y-m-d') . '.pdf'
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (Paciente $r) => throw_if($r->citas()->exists(), new \Exception('No se puede eliminar: tiene citas.'))),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_registro', 'desc')
            ->defaultPaginationPageOption(10) // ✅ Paginación optimizada
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('citas')); // ✅ EVITA N+1 QUERIES
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información del Paciente')
                    ->schema([
                        TextEntry::make('nombre')
                            ->label('Nombre Completo'),
                        TextEntry::make('ci')
                            ->label('Cédula de Identidad'),
                        TextEntry::make('edad')
                            ->label('Edad'),
                        TextEntry::make('telefono')
                            ->label('Teléfono'),
                        TextEntry::make('email')
                            ->label('Correo Electrónico'),
                        TextEntry::make('actividad_fisica')
                            ->label('Actividad Física')
                            ->badge()
                            ->colors([
                                'success' => 'competencia',
                                'primary' => 'salud',
                                'gray' => 'ninguna',
                            ])
                            ->formatStateUsing(fn ($state): string => match($state) {
                                'competencia' => '🏆 Competencia Deportiva',
                                'salud' => '💚 Salud / Bienestar',
                                'ninguna' => '⚪ Ninguna',
                                default => $state ?? 'Sin definir',
                            }),
                        
                        TextEntry::make('fecha_registro')
                            ->label('Fecha de Registro')
                            ->date('d/m/Y'),
                        TextEntry::make('created_at')
                            ->label('Registrado en sistema')
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
            'index' => Pages\ListPacientes::route('/'),
            'create' => Pages\CreatePaciente::route('/create'),
            'view' => Pages\ViewPaciente::route('/{record}'),
            'edit' => Pages\EditPaciente::route('/{record}/edit'),
        ];
    }

    public static function canDelete($record): bool
    {
        return !$record->citas()->exists();
    }

    public static function getLabel(): ?string
    {
        return 'Paciente';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Pacientes';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pacientes';
    }
}