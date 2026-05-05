<?php

namespace App\Http\Controllers;

use App\Models\HistorialClinico;
use App\Models\Paciente;
use Illuminate\Http\Request;

class ConsultaPacienteController extends Controller
{
    /**
     * Mostrar formulario de búsqueda
     */
    public function mostrarFormulario()
    {
        return view('consulta-paciente');
    }

    /**
     * Buscar paciente y mostrar resultados
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'tipo_busqueda' => 'required|in:ci,email,codigo',
            'valor_busqueda' => 'required|string',
        ]);

        $historiales = collect();
        $paciente = null;

        if ($request->tipo_busqueda === 'ci') {
            $paciente = Paciente::where('ci', $request->valor_busqueda)->first();
        } elseif ($request->tipo_busqueda === 'email') {
            $paciente = Paciente::where('email', $request->valor_busqueda)->first();
        } elseif ($request->tipo_busqueda === 'codigo') {
            $historial = HistorialClinico::where('codigo_acceso', $request->valor_busqueda)
                ->with(['cita.paciente', 'materialesUsados.material', 'servicios'])
                ->first();
            
            if ($historial) {
                return view('resultado-consulta', compact('historial'));
            }
        }

        if ($paciente) {
            $historiales = HistorialClinico::with(['cita.paciente', 'materialesUsados.material', 'servicios'])
                ->whereHas('cita', function ($query) use ($paciente) {
                    $query->where('pacientes_id', $paciente->id)
                          ->where('estado', 'realizado');
                })
                ->orderByDesc('created_at')
                ->get();

            return view('resultado-consulta', compact('paciente', 'historiales'));
        }

        return back()->with('error', 'No se encontraron resultados. Verifica los datos e intenta nuevamente.');
    }

    /**
     * Ver un historial específico con ejercicios
     */
    public function verHistorial($codigo)
    {
        $historial = HistorialClinico::where('codigo_acceso', $codigo)
            ->with([
                'cita.paciente',
                'materialesUsados.material',
                'servicios',
                'cita.paciente.ejercicios'
            ])
            ->firstOrFail();

        return view('resultado-consulta-detalle', compact('historial'));
    }
}