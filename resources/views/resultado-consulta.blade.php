<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Alex+Brush&display=swap" rel="stylesheet">
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Resultados - Isa Villegas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .font-logo { font-family: 'Montserrat', sans-serif; }
        .font-carta { font-family: 'Alex Brush', cursive; }
        
        @media print {
            nav, .no-print, button { display: none !important; }
            body { background: white; font-size: 11pt; }
            .bg-white { box-shadow: none !important; border: 1px solid #ddd !important; }
            .shadow-lg { box-shadow: none !important; }
            a { text-decoration: none; color: inherit; }
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <nav class="bg-white shadow-sm p-4 sticky top-0 z-50 no-print">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="font-logo text-2xl font-bold text-[#6a1b9a]">Isa Villegas</h1>
            <a href="{{ route('consulta.formulario') }}" class="text-[#00acc1] hover:text-[#6a1b9a] font-medium">
                ← Nueva búsqueda
            </a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-6 py-8">
        
        @if(isset($paciente))
            <div class="bg-white p-6 rounded-2xl shadow-lg mb-8">
                <h2 class="text-2xl font-bold text-[#6a1b9a] mb-2">
                    👤 {{ $paciente->nombre }}
                </h2>
                <p class="text-slate-600">
                    C.I.: {{ $paciente->ci }} | 
                    📞 {{ $paciente->telefono }} | 
                    📧 {{ $paciente->email }}
                </p>
            </div>

            @forelse($historiales as $historial)
                <div class="bg-white p-6 rounded-2xl shadow-lg mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">
                                Consulta del {{ $historial->created_at->format('d/m/Y') }}
                            </h3>
                            <p class="text-sm text-slate-500">
                                Hora: {{ $historial->created_at->format('H:i') }}
                            </p>
                        </div>
                        @if($historial->codigo_acceso)
                            <div class="bg-[#00acc1]/10 px-4 py-2 rounded-lg">
                                <p class="text-xs text-[#00acc1] font-semibold">Código:</p>
                                <p class="text-lg font-bold text-[#00acc1]">{{ $historial->codigo_acceso }}</p>
                            </div>
                        @endif
                    </div>

                    @if($historial->evaluacion)
                        <div class="mb-4">
                            <h4 class="font-semibold text-[#6a1b9a] mb-2">📝 Evaluación:</h4>
                            <p class="text-slate-700 bg-slate-50 p-4 rounded-xl whitespace-pre-wrap">{{ $historial->evaluacion }}</p>
                        </div>
                    @endif

                    @if($historial->recomendaciones)
                        <div class="mb-4">
                            <h4 class="font-semibold text-[#6a1b9a] mb-2">💡 Recomendaciones:</h4>
                            <p class="text-slate-700 bg-slate-50 p-4 rounded-xl whitespace-pre-wrap">{{ $historial->recomendaciones }}</p>
                        </div>
                    @endif

                    @if($historial->ejerciciosRecomendados->isNotEmpty())
                        <div class="mb-6">
                            <h4 class="font-semibold text-[#6a1b9a] mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                Ejercicios Recomendados para esta consulta
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($historial->ejerciciosRecomendados as $recomendacion)
                                    <div class="border-2 border-slate-100 rounded-xl p-5 hover:border-[#6a1b9a] transition-all bg-gradient-to-br from-white to-[#f5f3ff]">
                                        <div class="flex items-start justify-between mb-3">
                                            <h5 class="font-bold text-lg text-[#6a1b9a]">
                                                {{ $recomendacion->ejercicio->nombre_ejercicio }}
                                            </h5>
                                            @if($recomendacion->ejercicio->video_url)
                                                <a href="{{ $recomendacion->ejercicio->video_url }}" 
                                                   target="_blank"
                                                   class="text-[#00acc1] hover:text-[#6a1b9a] transition-colors p-2 rounded-full hover:bg-[#00acc1]/10"
                                                   title="Ver video demostrativo">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                        
                                        @if($recomendacion->ejercicio->descripcion)
                                            <p class="text-sm text-slate-600 mb-4">{{ $recomendacion->ejercicio->descripcion }}</p>
                                        @endif
                                        
                                        <div class="grid grid-cols-3 gap-2 mb-4">
                                            <div class="text-center bg-[#6a1b9a]/10 px-2 py-2 rounded-lg">
                                                <div class="text-[10px] uppercase text-slate-500 font-semibold">Series</div>
                                                <div class="font-bold text-[#6a1b9a]">{{ $recomendacion->series }}</div>
                                            </div>
                                            <div class="text-center bg-[#6a1b9a]/10 px-2 py-2 rounded-lg">
                                                <div class="text-[10px] uppercase text-slate-500 font-semibold">Reps</div>
                                                <div class="font-bold text-[#6a1b9a]">{{ $recomendacion->repeticiones }}</div>
                                            </div>
                                            <div class="text-center bg-[#6a1b9a]/10 px-2 py-2 rounded-lg">
                                                <div class="text-[10px] uppercase text-slate-500 font-semibold">Frec.</div>
                                                <div class="font-bold text-[#6a1b9a] text-[11px]">{{ $recomendacion->frecuencia }}</div>
                                            </div>
                                        </div>
                                        
                                        @if($recomendacion->notas_adicionales)
                                            <div class="bg-[#00acc1]/10 border-l-4 border-[#00acc1] p-3 rounded-r-lg">
                                                <p class="text-xs text-slate-700">
                                                    <span class="font-semibold">💡 Nota:</span> {{ $recomendacion->notas_adicionales }}
                                                </p>
                                            </div>
                                        @endif
                                        
                                        <p class="text-[10px] text-slate-400 mt-3 text-right">
                                            Asignado: {{ \Carbon\Carbon::parse($recomendacion->fecha_asignacion)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4 p-3 bg-[#fff3e0] border border-[#ffb74d] rounded-xl">
                                <p class="text-xs text-[#e65100] flex items-start gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>
                                        <strong>Importante:</strong> Realiza estos ejercicios bajo supervisión profesional. Si sientes dolor intenso, detente y consulta con tu fisioterapeuta.
                                    </span>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($historial->materialesUsados->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="font-semibold text-[#6a1b9a] mb-2">📦 Materiales Utilizados:</h4>
                            <ul class="list-disc list-inside text-slate-700 bg-slate-50 p-4 rounded-xl">
                                @foreach($historial->materialesUsados as $material)
                                    <li>{{ $material->material->nombre }} ({{ $material->cantidad_usada }} {{ $material->material->unidad }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($historial->servicios->isNotEmpty())
                        <div>
                            <h4 class="font-semibold text-[#6a1b9a] mb-2">💰 Servicios Realizados:</h4>
                            <ul class="space-y-2">
                                @foreach($historial->servicios as $servicio)
                                    <li class="flex justify-between bg-slate-50 p-3 rounded-lg">
                                        <span class="text-slate-700">{{ $servicio->nombre_servicio }}</span>
                                        <span class="font-semibold text-[#6a1b9a]">Bs {{ number_format($servicio->precio, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white p-12 rounded-2xl shadow-lg text-center">
                    <p class="text-slate-500 text-lg">No se encontraron consultas realizadas.</p>
                </div>
            @endforelse

        @elseif(isset($historial))
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-[#6a1b9a]">
                        Resultado de Consulta
                    </h2>
                    <p class="text-slate-600">{{ $historial->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="font-semibold text-slate-800 mb-2">Paciente:</h3>
                    <p class="text-slate-700">{{ $historial->cita->paciente->nombre }}</p>
                </div>

                @if($historial->evaluacion)
                    <div class="mb-6">
                        <h3 class="font-semibold text-[#6a1b9a] mb-2">Evaluación:</h3>
                        <p class="text-slate-700 bg-slate-50 p-4 rounded-xl whitespace-pre-wrap">{{ $historial->evaluacion }}</p>
                    </div>
                @endif

                @if($historial->recomendaciones)
                    <div class="mb-6">
                        <h3 class="font-semibold text-[#6a1b9a] mb-2">Recomendaciones:</h3>
                        <p class="text-slate-700 bg-slate-50 p-4 rounded-xl whitespace-pre-wrap">{{ $historial->recomendaciones }}</p>
                    </div>
                @endif

                @if($historial->ejerciciosRecomendados->isNotEmpty())
                    <div class="mb-6">
                        <h3 class="font-semibold text-[#6a1b9a] mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Ejercicios Recomendados
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($historial->ejerciciosRecomendados as $recomendacion)
                                <div class="border-2 border-slate-100 rounded-xl p-5 bg-gradient-to-br from-white to-[#f5f3ff]">
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="font-bold text-lg text-[#6a1b9a]">
                                            {{ $recomendacion->ejercicio->nombre_ejercicio }}
                                        </h4>
                                        @if($recomendacion->ejercicio->video_url)
                                            <a href="{{ $recomendacion->ejercicio->video_url }}" 
                                               target="_blank"
                                               class="text-[#00acc1] hover:text-[#6a1b9a] transition-colors"
                                               title="Ver video">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                    
                                    @if($recomendacion->ejercicio->descripcion)
                                        <p class="text-sm text-slate-600 mb-3">{{ $recomendacion->ejercicio->descripcion }}</p>
                                    @endif
                                    
                                    <div class="flex gap-4 mb-3 text-sm">
                                        <span class="bg-[#6a1b9a]/10 px-3 py-1 rounded-full font-semibold text-[#6a1b9a]">
                                            {{ $recomendacion->series }} series
                                        </span>
                                        <span class="bg-[#6a1b9a]/10 px-3 py-1 rounded-full font-semibold text-[#6a1b9a]">
                                            {{ $recomendacion->repeticiones }} reps
                                        </span>
                                        <span class="bg-[#6a1b9a]/10 px-3 py-1 rounded-full font-semibold text-[#6a1b9a]">
                                            {{ $recomendacion->frecuencia }}
                                        </span>
                                    </div>
                                    
                                    @if($recomendacion->notas_adicionales)
                                        <div class="bg-[#00acc1]/10 border-l-4 border-[#00acc1] p-3 rounded-r-lg">
                                            <p class="text-xs text-slate-700">
                                                <span class="font-semibold">💡 Nota:</span> {{ $recomendacion->notas_adicionales }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 p-3 bg-[#fff3e0] border border-[#ffb74d] rounded-xl">
                            <p class="text-xs text-[#e65100] flex items-start gap-2">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>
                                    <strong>Importante:</strong> Realiza estos ejercicios bajo supervisión profesional.
                                </span>
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if(isset($historial) && !isset($paciente))
            <div class="text-center mt-8 no-print">
                <button onclick="window.print()" 
                        class="inline-flex items-center gap-2 bg-[#6a1b9a] text-white px-6 py-3 rounded-full font-semibold hover:bg-[#4a148c] transition-all shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    🖨️ Imprimir mi rutina
                </button>
            </div>
        @endif
    </div>

</body>
</html>