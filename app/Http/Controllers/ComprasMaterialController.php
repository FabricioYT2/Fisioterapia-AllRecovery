<?php

namespace App\Http\Controllers;

use App\Models\CompraMaterial;
use App\Models\DetalleCompraMaterial;
use App\Models\Material;
use Illuminate\Http\Request;

class ComprasMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $compras = CompraMaterial::with('detalles.material')->orderByDesc('fecha_compra')->get();

        return view('compras_material.index', compact('compras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $materiales = Material::orderBy('nombre', 'asc')->get();

        return view('compras_material.create', compact('materiales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha_compra' => 'required|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.materiales_id' => 'required|exists:materiales,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $compra = CompraMaterial::create([
            'fecha_compra' => $data['fecha_compra'],
            'costo_total' => 0,
        ]);

        $total = 0;

        foreach ($data['detalles'] as $detalle) {
            $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];

            $compra->detalles()->create([
                'materiales_id' => $detalle['materiales_id'],
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'subtotal' => $subtotal,
            ]);
            
            // Incrementar stock del material
            Material::find($detalle['materiales_id'])->increment('stock_actual', $detalle['cantidad']);

            $total += $subtotal;
        }

        $compra->update(['costo_total' => $total]);

        return redirect()->route('compras_material.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    }
}
