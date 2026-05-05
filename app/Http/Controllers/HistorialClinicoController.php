<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\HistorialClinico;
use App\Models\HistorialMaterial;
use App\Models\Material;
use App\Models\Servicio;
use Illuminate\Http\Request;

class HistorialClinicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $historiales = HistorialClinico::with(['cita.paciente', 'materialesUsados.material', 'servicios'])
            ->orderByDesc('created_at')
            ->get();

        return view('historial_clinico.index', compact('historiales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $citas = Cita::with('paciente')->whereDoesntHave('historialClinico')->orderByDesc('fecha_hora')->get();
        $materiales = Material::orderBy('nombre', 'asc')->get();

        return view('historial_clinico.create', compact('citas', 'materiales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'citas_id' => 'required|exists:citas,id',
            'evaluacion' => 'required|string',
            'recomendaciones' => 'nullable|string',
            'recetas_compra' => 'nullable|string',
            'materiales_usados' => 'nullable|array',
            'materiales_usados.*.materiales_id' => 'required|exists:materiales,id',
            'materiales_usados.*.cantidad_usada' => 'required|integer|min:1',
            'servicios' => 'nullable|array',
            'servicios.*.nombre_servicio' => 'required|string',
            'servicios.*.precio' => 'required|numeric|min:0',
            'servicios.*.duracion_minutos' => 'required|integer|min:1',
        ]);

        $historial = HistorialClinico::create([
            'citas_id' => $data['citas_id'],
            'evaluacion' => $data['evaluacion'],
            'recomendaciones' => $data['recomendaciones'] ?? null,
            'recetas_compra' => $data['recetas_compra'] ?? null,
        ]);

        if (isset($data['materiales_usados'])) {
            foreach ($data['materiales_usados'] as $material) {
                $historial->materialesUsados()->create([
                    'materiales_id' => $material['materiales_id'],
                    'cantidad_usada' => $material['cantidad_usada'],
                ]);
                
                // Decrementar stock del material
                Material::find($material['materiales_id'])->decrement('stock_actual', $material['cantidad_usada']);
            }
        }

        if (isset($data['servicios'])) {
            foreach ($data['servicios'] as $servicio) {
                $historial->servicios()->create([
                    'nombre_servicio' => $servicio['nombre_servicio'],
                    'precio' => $servicio['precio'],
                    'duracion_minutos' => $servicio['duracion_minutos'],
                ]);
            }
        }

        return redirect()->route('historial_clinico.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $historial = HistorialClinico::with(['cita.paciente', 'materialesUsados.material', 'servicios'])->findOrFail($id);

        return view('historial_clinico.show', compact('historial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $historial = HistorialClinico::with(['materialesUsados', 'servicios'])->findOrFail($id);
        $citas = Cita::with('paciente')->orderByDesc('fecha_hora')->get();
        $materiales = Material::orderBy('nombre', 'asc')->get();

        return view('historial_clinico.edit', compact('historial', 'citas', 'materiales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'citas_id' => 'required|exists:citas,id',
            'evaluacion' => 'required|string',
            'recomendaciones' => 'nullable|string',
            'recetas_compra' => 'nullable|string',
            'materiales_usados' => 'nullable|array',
            'materiales_usados.*.materiales_id' => 'required|exists:materiales,id',
            'materiales_usados.*.cantidad_usada' => 'required|integer|min:1',
            'servicios' => 'nullable|array',
            'servicios.*.nombre_servicio' => 'required|string',
            'servicios.*.precio' => 'required|numeric|min:0',
            'servicios.*.duracion_minutos' => 'required|integer|min:1',
        ]);

        $historial = HistorialClinico::findOrFail($id);
        $historial->update([
            'citas_id' => $data['citas_id'],
            'evaluacion' => $data['evaluacion'],
            'recomendaciones' => $data['recomendaciones'] ?? null,
            'recetas_compra' => $data['recetas_compra'] ?? null,
        ]);

        // Recuperar materiales viejos para devolver stock
        $materialesViejos = $historial->materialesUsados()->get();
        foreach ($materialesViejos as $materialViejo) {
            Material::find($materialViejo->materiales_id)->increment('stock_actual', $materialViejo->cantidad_usada);
        }

        $historial->materialesUsados()->delete();
        $historial->servicios()->delete();

        if (isset($data['materiales_usados'])) {
            foreach ($data['materiales_usados'] as $material) {
                $historial->materialesUsados()->create([
                    'materiales_id' => $material['materiales_id'],
                    'cantidad_usada' => $material['cantidad_usada'],
                ]);
                
                // Decrementar stock del material
                Material::find($material['materiales_id'])->decrement('stock_actual', $material['cantidad_usada']);
            }
        }

        if (isset($data['servicios'])) {
            foreach ($data['servicios'] as $servicio) {
                $historial->servicios()->create([
                    'nombre_servicio' => $servicio['nombre_servicio'],
                    'precio' => $servicio['precio'],
                    'duracion_minutos' => $servicio['duracion_minutos'],
                ]);
            }
        }

        return redirect()->route('historial_clinico.show', $historial);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $historial = HistorialClinico::findOrFail($id);
        
        // Devolver stock de los materiales usados
        $materiales = $historial->materialesUsados()->get();
        foreach ($materiales as $material) {
            Material::find($material->materiales_id)->increment('stock_actual', $material->cantidad_usada);
        }
        
        $historial->delete();

        return redirect()->route('historial_clinico.index');
    }
}
