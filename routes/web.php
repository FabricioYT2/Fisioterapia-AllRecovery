<?php

use Illuminate\Support\Facades\Route;   
use App\Http\Controllers\CitaPublicaController;
use App\Http\Controllers\ConsultaPacienteController;
use App\Models\TurnoConfig;
use Illuminate\Http\Request;


Route::post('/solicitar-cita', [CitaPublicaController::class, 'crearCitaPublica'])
    ->name('citas.public.store');
Route::get('/consultar-resultados', [ConsultaPacienteController::class, 'mostrarFormulario'])
    ->name('consulta.formulario');

Route::post('/consultar-resultados', [ConsultaPacienteController::class, 'buscar'])
    ->name('consulta.buscar');

Route::get('/api/turnos-disponibles', function (Request $request) {
    $fecha = $request->input('fecha');
    
    if (!$fecha) {
        return response()->json(['error' => 'Fecha requerida'], 400);
    }

    $turnos = TurnoConfig::getTurnos();
    $resultado = [];

    foreach ($turnos as $key => $turno) {
        $disponibles = TurnoConfig::getCuposDisponibles($fecha, $key);
        
        $resultado[$key] = [
            'nombre' => "{$turno['icono']} {$turno['nombre']}",
            'hora_inicio' => $turno['hora_inicio'],
            'hora_fin' => $turno['hora_fin'],
            'cupos_maximos' => $turno['cupos_maximos'],
            'disponible' => $disponibles,
        ];
    }

    return response()->json($resultado);
});

Route::get('/debug-cupos', function() {
    $fecha = request('fecha', date('Y-m-d'));
    $turno = request('turno', 'manana');
    
    $citas = \App\Models\Cita::whereDate('fecha_hora', $fecha)
        ->where('turno', $turno)
        ->get();
    
    return [
        'fecha' => $fecha,
        'turno' => $turno,
        'citas_encontradas' => $citas->count(),
        'citas' => $citas->map(fn($c) => [
            'id' => $c->id,
            'paciente' => $c->paciente->nombre,
            'fecha_hora' => $c->fecha_hora,
            'turno' => $c->turno,
            'estado' => $c->estado,
        ]),
        'cupos_disponibles' => \App\Models\TurnoConfig::getCuposDisponibles($fecha, $turno),
    ];
});



Route::get('/resultado/{codigo}', [ConsultaPacienteController::class, 'verHistorial'])
    ->name('consulta.resultado');
Route::get('/', function () {
    return view('welcome');
});
