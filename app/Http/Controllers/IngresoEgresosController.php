<?php

namespace App\Http\Controllers;

use App\Models\Cobro;
use App\Models\CompraMaterial;
use App\Models\IngresoEgreso;
use Illuminate\Http\Request;

class IngresoEgresosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $registros = IngresoEgreso::with(['cobro.cita.paciente', 'compraMaterial'])
            ->orderByDesc('fecha')
            ->get();

        return view('ingresos_egresos.index', compact('registros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cobros = Cobro::with('cita.paciente')->orderByDesc('fecha_emision')->get();
        $compras = CompraMaterial::orderByDesc('fecha_compra')->get();

        return view('ingresos_egresos.create', compact('cobros', 'compras'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'descripcion' => 'required|string',
            'cobros_id' => 'nullable|exists:cobros,id',
            'compra_materiales_id' => 'nullable|exists:compra_materiales,id',
        ]);

        if ($data['tipo'] === 'ingreso') {
            if (empty($data['cobros_id'])) {
                return back()->withErrors(['cobros_id' => 'Debe seleccionar un cobro para un ingreso.'])->withInput();
            }
            if (!empty($data['compra_materiales_id'])) {
                return back()->withErrors(['compra_materiales_id' => 'No se puede vincular una compra en un ingreso.'])->withInput();
            }
        }

        if ($data['tipo'] === 'egreso') {
            if (empty($data['compra_materiales_id'])) {
                return back()->withErrors(['compra_materiales_id' => 'Debe seleccionar una compra de material para un egreso.'])->withInput();
            }
            if (!empty($data['cobros_id'])) {
                return back()->withErrors(['cobros_id' => 'No se puede vincular un cobro en un egreso.'])->withInput();
            }
        }

        IngresoEgreso::create($data);

        return redirect()->route('ingresos_egresos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $registro = IngresoEgreso::with(['cobro.cita.paciente', 'compraMaterial'])->findOrFail($id);

        return view('ingresos_egresos.show', compact('registro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $registro = IngresoEgreso::findOrFail($id);
        $cobros = Cobro::with('cita.paciente')->orderByDesc('fecha_emision')->get();
        $compras = CompraMaterial::orderByDesc('fecha_compra')->get();

        return view('ingresos_egresos.edit', compact('registro', 'cobros', 'compras'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'descripcion' => 'required|string',
            'cobros_id' => 'nullable|exists:cobros,id',
            'compra_materiales_id' => 'nullable|exists:compra_materiales,id',
        ]);

        if ($data['tipo'] === 'ingreso') {
            if (empty($data['cobros_id'])) {
                return back()->withErrors(['cobros_id' => 'Debe seleccionar un cobro para un ingreso.'])->withInput();
            }
            if (!empty($data['compra_materiales_id'])) {
                return back()->withErrors(['compra_materiales_id' => 'No se puede vincular una compra en un ingreso.'])->withInput();
            }
        }

        if ($data['tipo'] === 'egreso') {
            if (empty($data['compra_materiales_id'])) {
                return back()->withErrors(['compra_materiales_id' => 'Debe seleccionar una compra de material para un egreso.'])->withInput();
            }
            if (!empty($data['cobros_id'])) {
                return back()->withErrors(['cobros_id' => 'No se puede vincular un cobro en un egreso.'])->withInput();
            }
        }

        $registro = IngresoEgreso::findOrFail($id);
        $registro->update($data);

        return redirect()->route('ingresos_egresos.show', $registro);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $registro = IngresoEgreso::findOrFail($id);
        $registro->delete();

        return redirect()->route('ingresos_egresos.index');
    }
}
