<?php

namespace App\Models;

class TurnoConfig
{

    
    public static function getTurnos(): array
    {
        return [
            'manana' => [
                'nombre' => 'Mañana',
                'hora_inicio' => '08:00',
                'hora_fin' => '12:00',
                'cupos_maximos' => 3,
                'icono' => '🌅',
            ],
            'tarde' => [
                'nombre' => 'Tarde',
                'hora_inicio' => '14:00',
                'hora_fin' => '18:00',
                'cupos_maximos' => 3,
                'icono' => '🌆',
            ],
        ];
    }

    public static function getCuposDisponibles(string $fecha, string $turno): int
    {
        $config = self::getTurnos()[$turno] ?? null;
        if (!$config) return 0;

        $hoy = now()->format('Y-m-d');
        if ($fecha === $hoy) {
            $horaFin = $config['hora_fin']; // Ej: '12:00' o '18:00'
            $horaActual = now()->format('H:i');

            if ($horaActual >= $horaFin) {
                return 0; // ⛔ Turno finalizado
            }
        }

        $fechaYMD = \Carbon\Carbon::parse($fecha)->format('Y-m-d');

        $cuposOcupados = \App\Models\Cita::whereDate('fecha_hora', $fechaYMD)
            ->where('turno', $turno)
            ->where('estado', '!=', 'cancelado')
            ->count();

        return max(0, $config['cupos_maximos'] - $cuposOcupados);
        $fechaObj = \Carbon\Carbon::parse($fecha);
        $fechaYMD = $fechaObj->format('Y-m-d');

        \Log::info('Buscando cupos para:', [
            'fecha' => $fechaYMD,
            'turno' => $turno,
        ]);

        $cuposOcupados = \App\Models\Cita::whereDate('fecha_hora', $fechaYMD)
            ->where('turno', $turno)
            ->where('estado', '!=', 'cancelado')
            ->count();
        \Log::info('Cupos ocupados:', ['count' => $cuposOcupados]);

        $disponibles = max(0, $config['cupos_maximos'] - $cuposOcupados);
        
        \Log::info('Cupos disponibles:', ['disponibles' => $disponibles]);
        
        return $disponibles;
    }

    public static function hayCuposDisponibles(string $fecha, string $turno): bool
    {
        return self::getCuposDisponibles($fecha, $turno) > 0;
    }
}