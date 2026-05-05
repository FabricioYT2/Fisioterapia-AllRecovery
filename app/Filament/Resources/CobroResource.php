<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CobroResource\Pages;
use App\Models\Cobro;
use App\Models\Cita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Database\Eloquent\Builder;

class CobroResource extends Resource
{
    protected static ?string $model = Cobro::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = '💰 Finanzas';
    protected static ?int $navigationSort = 31;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cobro')
                    ->schema([
                        Forms\Components\Select::make('citas_id')
                            ->label('Cita')
                            ->relationship('cita', 'motivo', fn(Builder $query) => $query->where('estado', 'realizado'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn(Cita $record): string => "{$record->paciente->nombre} - {$record->motivo}")
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('monto_total')
                            ->label('Monto Total (Bs)')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('Bs')
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('fecha_emision')
                            ->label('Fecha de Emisión')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        Forms\Components\Select::make('estado')
                            ->label('Estado de Pago')
                            ->options([
                                'pendiente' => '⏳ Pendiente',
                                'parcial' => '💵 Parcial',
                                'pagado' => '✅ Pagado',
                            ])
                            ->default('pendiente')
                            ->required()
                            ->columnSpan(1),
                    ])->columns(2),
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
                    ->label('Fecha Cita')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('monto_total')
                    ->label('Monto Total')
                    ->money('BOB', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'pendiente',
                        'info' => 'parcial',
                        'success' => 'pagado',
                    ])
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'pendiente' => '⏳ Pendiente',
                        'parcial' => '💵 Parcial',
                        'pagado' => '✅ Pagado',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('fecha_emision')
                    ->label('Fecha Emisión')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Por Estado')
                    ->options([
                        'pendiente' => 'Pendientes',
                        'parcial' => 'Parciales',
                        'pagado' => 'Pagados',
                    ]),
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
            ->defaultSort('fecha_emision', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detalles del Cobro')
                    ->schema([
                        TextEntry::make('cita.paciente.nombre')->label('Paciente'),
                        TextEntry::make('cita.motivo')->label('Motivo'),
                        TextEntry::make('monto_total')->label('Monto Total')->money('BOB'),
                        TextEntry::make('fecha_emision')->label('Fecha Emisión')->date('d/m/Y'),
                        TextEntry::make('estado')
                            ->label('Estado')
                            ->badge()
                            ->colors([
                                'warning' => 'pendiente',
                                'info' => 'parcial',
                                'success' => 'pagado',
                            ])
                            ->formatStateUsing(fn ($state): string => match($state) {
                                'pendiente' => '⏳ Pendiente',
                                'parcial' => '💵 Parcial',
                                'pagado' => '✅ Pagado',
                                default => $state ?? 'Sin definir',
                            }),
                        TextEntry::make('created_at')->label('Registrado')->dateTime('d/m/Y H:i'),
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
            'index' => Pages\ListCobros::route('/'),
            'create' => Pages\CreateCobro::route('/create'),
            'view' => Pages\ViewCobro::route('/{record}'),
            'edit' => Pages\EditCobro::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string { return 'Cobro'; }
    public static function getPluralLabel(): ?string { return 'Cobros'; }
    public static function getNavigationLabel(): string { return 'Cobros'; }
}