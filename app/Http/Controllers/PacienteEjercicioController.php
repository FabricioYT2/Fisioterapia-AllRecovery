<?php

namespace App\Http\Controllers;

use App\Models\PacienteEjercicio;
use Illuminate\Http\Request;

class PacienteEjercicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paciente_ejercicios = PacienteEjercicio::latest()->paginate(15);
        return view('paciente_ejercicios.index', compact('paciente_ejercicios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('paciente_ejercicios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pacientes_id' => 'required',
            'ejercicios_id' => 'required',
            'fecha_asignacion' => 'required',
            'series' => 'required',
            'repeticiones' => 'required',
            'frecuencia' => 'required',
        ]);

        PacienteEjercicio::create($request->all());
        return redirect()->route('paciente_ejercicios.index')->with('success', 'Ejercicio con paciente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PacienteEjercicio $paciente_ejercicio)
    {
        return view('paciente_ejercicios.show', compact('paciente_ejercicio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PacienteEjercicio $paciente_ejercicio)
    {
        return view('paciente_ejercicios.edit', compact('paciente_ejercicio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PacienteEjercicio $paciente_ejercicio)
    {
        $request->validate([
            'pacientes_id' => 'required',
            'ejercicios_id' => 'required',
            'fecha_asignacion' => 'required',
            'series' => 'required',
            'repeticiones' => 'required',
            'frecuencia' => 'required',
        ]);

        $paciente_ejercicio->update($request->all());
        return redirect()->route('paciente_ejercicios.index')->with('success', 'Ejercicio con paciente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PacienteEjercicio $paciente_ejercicio)
    {
        $paciente_ejercicio->delete();
        return redirect()->route('paciente_ejercicios.index')->with('success', 'Ejercicio con paciente eliminado exitosamente.');
    }
}
