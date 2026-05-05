<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materiales = Material::latest()->paginate(15);
        return view('materiales.index', compact('materiales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('materiales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'stock_actual' => 'required',
            'stock_minimo' => 'required',
            'unidad' => 'required',
        ]);

        Material::create($request->all());
        return redirect()->route('materiales.index')->with('success', 'Material creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        return view('materiales.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        return view('materiales.edit', compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'nombre' => 'required',
            'stock_actual' => 'required',
            'stock_minimo' => 'required',
            'unidad' => 'required',
        ]);

        $material->update($request->all());
        return redirect()->route('materiales.index')->with('success', 'Material actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        if ($material->historialesUso()->exists()){
            return redirect()->route('materiales.index')->with('error', 'No se puede borrar material porque se uso en un historial clinico.');
        }

        $material->delete();
        return redirect()->route('materiales.index')->with('success', 'Material eliminado exitosamente.');
    }
}
