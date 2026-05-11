<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Alex+Brush&display=swap" rel="stylesheet">
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consultar Resultados - Isa Villegas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .font-logo { font-family: 'Montserrat', sans-serif; }
        .font-carta { font-family: 'Alex Brush', cursive; }
    </style>
</head>
<body class="bg-gradient-to-br from-[#f5f3ff] to-white min-h-screen">
    
    <nav class="bg-white shadow-sm p-4">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="font-logo text-2xl font-bold text-[#6a1b9a]">Isa Villegas</h1>
            <a href="{{ url('/') }}" class="text-[#00acc1] hover:text-[#6a1b9a] font-medium">
                ← Volver al inicio
            </a>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-6 py-16">
        <div class="text-center mb-12">
            <h2 class="font-logo text-4xl font-bold text-[#6a1b9a] mb-4">
                Consulta tus Resultados
            </h2>
            <p class="text-slate-600 text-lg">
                Ingresa tus datos para ver tu historial de consultas y ejercicios recomendados
            </p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('consulta.buscar') }}" method="POST" class="bg-white p-8 rounded-3xl shadow-xl">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-3">
                    Tipo de Búsqueda
                </label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="tipo_busqueda" value="ci" class="peer sr-only" checked>
                        <div class="text-center p-3 rounded-xl border-2 border-slate-200 peer-checked:border-[#6a1b9a] peer-checked:bg-[#6a1b9a] peer-checked:text-white transition-all">
                             C.I.
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="tipo_busqueda" value="email" class="peer sr-only">
                        <div class="text-center p-3 rounded-xl border-2 border-slate-200 peer-checked:border-[#6a1b9a] peer-checked:bg-[#6a1b9a] peer-checked:text-white transition-all">
                            📧 Email
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="tipo_busqueda" value="codigo" class="peer sr-only">
                        <div class="text-center p-3 rounded-xl border-2 border-slate-200 peer-checked:border-[#6a1b9a] peer-checked:bg-[#6a1b9a] peer-checked:text-white transition-all">
                            🔑 Código
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Ingresa el valor
                </label>
                <input type="text" 
                       name="valor_busqueda" 
                       required
                       placeholder="Ej: 1234567 o tu@email.com o ABC123XY"
                       class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-[#6a1b9a] focus:outline-none transition-colors">
            </div>

            <button type="submit" 
                    class="w-full bg-[#6a1b9a] text-white py-4 rounded-xl font-semibold text-lg hover:bg-[#4a148c] transition-all shadow-lg hover:shadow-xl">
                🔍 Buscar mis Resultados
            </button>

            <p class="text-center text-xs text-slate-500 mt-4">
                🔒 Tus datos están protegidos. Solo tú puedes acceder a tu información.
            </p>
        </form>
    </div>

</body>
</html>