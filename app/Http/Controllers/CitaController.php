<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $citas = Cita::latest()->paginate(15);
        return view('citas.index', compact('citas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('citas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pacientes_id' => 'required',
            'fecha_hora' => 'required',
            'estado' => 'required',
            'motivo' => 'required',
        ]);

        Cita::create($request->all());
        return redirect()->route('citas.index')->with('success', 'Cita Creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cita $cita)
    {
        return view('citas.show', compact('cita'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cita $cita)
    {
        return view('citas.edit', compact('cita'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'pacientes_id' => 'required|exists:pacientes,id',
            'fecha_hora' => 'required',
            'estado' => 'required',
            'motivo' => 'required',
        ]);

        $cita->update($request->all());
        return redirect()->route('citas.index')->with('success', 'Cita actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cita $cita)
    {
        if ($cita->cobros()->exists()){
            return redirect()->route('citas.index')->with('error', 'No se puede eliminar citas porque tiene cobros.');
        }

        $cita->delete();
        return redirect()->route('citas.index')->with('success', 'Cita eliminado exitosamente.');
    }
}
