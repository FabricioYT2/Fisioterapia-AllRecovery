<?php

namespace App\Filament\Widgets;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Material;
use App\Models\IngresoEgreso;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $hoy = now();
        $mes = now()->startOfMonth();
        $citasHoy = Cita::whereDate('fecha_hora', $hoy)->count();
        $pacientesActivos = Paciente::count();
        $citasMes = Cita::where('fecha_hora', '>=', $mes)->count();
        $totalMateriales = Material::count();
        $stockBajo = Material::whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        
        $ingresos = IngresoEgreso::where('tipo', 'ingreso')->where('fecha', '>=', $mes)->sum('monto');
        $egresos = IngresoEgreso::where('tipo', 'egreso')->where('fecha', '>=', $mes)->sum('monto');
        $balance = $ingresos - $egresos;

        return [
            Stat::make('Citas Hoy', $citasHoy)
                ->description('Programadas para hoy')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),

            Stat::make('Pacientes Activos', $pacientesActivos)
                ->description('Total registrado')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Citas este Mes', $citasMes)
                ->description('Total del mes')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('info'),

            Stat::make('Total Materiales', $totalMateriales)
                ->description('En inventario')
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('info'),

            Stat::make('Stock Bajo', $stockBajo)
                ->description('Materiales por reabastecer')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($stockBajo > 0 ? 'warning' : 'success'),

            Stat::make('Balance', 'Bs ' . number_format($balance, 2))
                ->description($balance >= 0 ? '✅ Positivo' : '❌ Negativo')
                ->descriptionIcon($balance >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($balance >= 0 ? 'success' : 'danger'),

            Stat::make('Ingresos del Mes', 'Bs ' . number_format($ingresos, 2))
                ->description('💰 Total ingresos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Egresos del Mes', 'Bs ' . number_format($egresos, 2))
                ->description('📉 Total egresos')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}