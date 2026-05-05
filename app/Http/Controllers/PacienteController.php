<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteController extends Controller
{

    public function index()
    {
        $pacientes = Paciente::latest()->paginate(15);
        return view('pacientes.index', compact('pacientes'));
    }
    public function create()
    {
        return view('pacientes.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'ci' => 'required',
            'edad' => 'required',
            'telefono' => 'required',
            'email' => 'required',
            'actividad_fisica' => 'required',
            'fecha_registro' => 'required',
        ]);

        Paciente::create($request->all());
        return redirect()->route('pacientes.index')->with('success', 'Paciente creado exitosamente.');
    }
    public function show(Paciente $paciente)
    {
        return view('pacientes.show', compact('paciente'));
    }
    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }
    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'nombre' => 'required',
            'ci' => 'required',
            'edad' => 'required',
            'telefono' => 'required',
            'email' => 'required',
            'actividad_fisica' => 'required',
            'fecha_registro' => 'required',
        ]);

        $paciente->update($request->all());
        return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado exitosamente.');
    }
    public function destroy(Paciente $paciente)
    {
        if ($paciente->citas()->exists()){
            return redirect()->route('pacientes.index')->with('error', 'No se puede eliminar a paciente porque tiene citas registradas.');
        }

        $paciente->delete();
        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado exitosamente.');
    }
}
