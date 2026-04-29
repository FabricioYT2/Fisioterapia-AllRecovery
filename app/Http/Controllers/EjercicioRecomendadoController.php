<?php

namespace App\Http\Controllers;

use App\Models\Ejercicio;
use Illuminate\Http\Request;

class EjercicioRecomendadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ejercicios = Ejercicio::latest()->paginate(15);
        return view('ejercicios.index', compact('ejercicios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ejercicios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_ejercicio' => 'required',
            'descripcion' => 'required',
            'video_url' => 'required',
        ]);

        Ejercicio::create($request->all());
        return redirect()->route('ejercicios.index')->with('success', 'Ejercicio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ejercicio $ejercicio)
    {
        return view('ejercicios.show', compact('ejercicio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ejercicio $ejercicio)
    {
        return view('ejercicios.edit', compact('ejercicio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ejercicio $ejercicio)
    {
        $request->validate([
            'nombre_ejercicio' => 'required',
            'descripcion' => 'required',
            'video_url' => 'required',
        ]);

        $ejercicio->update($request->all());
        return redirect()->route('ejercicios.index')->with('success', 'Ejercicio creado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ejercicio $ejercicio)
    {
        if ($ejercicio->pacientes()->exists()){
            return redirect()->route('ejercicios.index')->with('error', 'No se puede eliminar el ejercicio porque tiene paciente que estan realizando el ejercicio.');
        }

        $ejercicio->delete();
        return redirect()->route('ejercicios.index')->with('success', 'Ejercicio eliminado exitosamente.');
    }
}
