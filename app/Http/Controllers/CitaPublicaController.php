<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TurnoConfig;

class CitaPublicaController extends Controller
{
    public function crearCitaPublica(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ci' => 'required|string|max:50',
            'edad' => 'required|numeric|min:1|max:120',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'actividad_fisica' => 'required|in:competencia,salud,ninguna',
            'fecha_preferida' => 'required|date|after_or_equal:today',
            'turno' => 'required|in:manana,tarde',
            'motivo' => 'required|string|max:500',
        ], [
            'turno.required' => 'Debe seleccionar un turno',
            'turno.in' => 'Turno no válido',
        ]);
        $cuposDisponibles = \App\Models\TurnoConfig::getCuposDisponibles(
            $validated['fecha_preferida'],
            $validated['turno']
        );

        if ($cuposDisponibles <= 0) {
            return back()
                ->withInput()
                ->withErrors(['turno' => 'Lo sentimos, no hay cupos disponibles para este turno. Por favor seleccione otra fecha o turno.']);
        }

        DB::transaction(function () use ($validated) {
            $paciente = Paciente::firstOrCreate(
                ['ci' => $validated['ci']],
                [
                    'nombre' => $validated['nombre'],
                    'edad' => $validated['edad'],
                    'telefono' => $validated['telefono'],
                    'email' => $validated['email'],
                    'actividad_fisica' => $validated['actividad_fisica'],
                    'fecha_registro' => now(),
                ]
            );

            $turnoConfig = TurnoConfig::getTurnos()[$validated['turno']];
            $hora_asignada = $turnoConfig['hora_inicio'];
            
            Cita::create([
                'pacientes_id' => $paciente->id,
                'fecha_hora' => "{$validated['fecha_preferida']} {$hora_asignada}",
                'turno' => $validated['turno'],
                'estado' => 'pendiente',
                'motivo' => $validated['motivo'],
                'fuente' => 'web',
            ]);
        });

        return back()->with('success', '✅ ¡Cita solicitada exitosamente! Te contactaremos para confirmar.');
    }
}