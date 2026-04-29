<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cobro;
use App\Models\CobroPendiente;
use Illuminate\Http\Request;

class CobroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cobros = Cobro::with(['cita.paciente', 'pagosPendientes'])
            ->orderByDesc('fecha_emision')
            ->get();

        return view('cobros.index', compact('cobros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $citas = Cita::with('paciente')->orderByDesc('fecha_hora')->get();

        return view('cobros.create', compact('citas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'citas_id' => 'required|exists:citas,id',
            'monto_total' => 'required|numeric|min:0',
            'fecha_emision' => 'required|date',
            'estado' => 'required|in:pagado,pendiente,parcial',
            'pagos_pendientes' => 'nullable|array',
            'pagos_pendientes.*.monto_pagado' => 'required|numeric|min:0',
            'pagos_pendientes.*.monto_adeudado' => 'required|numeric|min:0',
            'pagos_pendientes.*.fecha_pago' => 'required|date',
        ]);

        $cobro = Cobro::create([
            'citas_id' => $data['citas_id'],
            'monto_total' => $data['monto_total'],
            'fecha_emision' => $data['fecha_emision'],
            'estado' => $data['estado'],
        ]);

        if (isset($data['pagos_pendientes'])) {
            foreach ($data['pagos_pendientes'] as $pendiente) {
                $cobro->pagosPendientes()->create([
                    'monto_pagado' => $pendiente['monto_pagado'],
                    'monto_adeudado' => $pendiente['monto_adeudado'],
                    'fecha_pago' => $pendiente['fecha_pago'],
                ]);
            }
        }

        return redirect()->route('cobros.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cobro = Cobro::with(['cita.paciente', 'pagosPendientes'])->findOrFail($id);

        return view('cobros.show', compact('cobro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cobro = Cobro::with('pagosPendientes')->findOrFail($id);
        $citas = Cita::with('paciente')->orderByDesc('fecha_hora')->get();

        return view('cobros.edit', compact('cobro', 'citas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'citas_id' => 'required|exists:citas,id',
            'monto_total' => 'required|numeric|min:0',
            'fecha_emision' => 'required|date',
            'estado' => 'required|in:pagado,pendiente,parcial',
            'pagos_pendientes' => 'nullable|array',
            'pagos_pendientes.*.monto_pagado' => 'required|numeric|min:0',
            'pagos_pendientes.*.monto_adeudado' => 'required|numeric|min:0',
            'pagos_pendientes.*.fecha_pago' => 'required|date',
        ]);

        $cobro = Cobro::findOrFail($id);
        $cobro->update([
            'citas_id' => $data['citas_id'],
            'monto_total' => $data['monto_total'],
            'fecha_emision' => $data['fecha_emision'],
            'estado' => $data['estado'],
        ]);

        $cobro->pagosPendientes()->delete();

        if (isset($data['pagos_pendientes'])) {
            foreach ($data['pagos_pendientes'] as $pendiente) {
                $cobro->pagosPendientes()->create([
                    'monto_pagado' => $pendiente['monto_pagado'],
                    'monto_adeudado' => $pendiente['monto_adeudado'],
                    'fecha_pago' => $pendiente['fecha_pago'],
                ]);
            }
        }

        return redirect()->route('cobros.show', $cobro);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cobro = Cobro::findOrFail($id);
        $cobro->delete();

        return redirect()->route('cobros.index');
    }
}
