<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Alex+Brush&display=swap" rel="stylesheet">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Isa Villegas - Fisioterapia y Kinesiología</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-logo { font-family: 'Montserrat', sans-serif; letter-spacing: 0.05em; }
        .font-carta { 
            font-family: 'Alex Brush', cursive !important;
            font-weight: 400 !important;
            text-transform: none !important;
            letter-spacing: 0 !important;
        }
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.active { opacity: 1; transform: translateY(0); }
        
        .form-input {
            transition: all 0.2s ease;
            border: 2px solid #e2e8f0;
        }
        .form-input:focus {
            border-color: #6a1b9a;
            box-shadow: 0 0 0 3px rgba(106, 27, 154, 0.1);
            outline: none;
        }
        
        .stars { color: #f59e0b; font-size: 1.2rem; }
        
        .toast {
            position: fixed; top: 1rem; right: 1rem; z-index: 9999;
            padding: 1rem 1.5rem; border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease, fadeOut 0.3s ease 2.7s forwards;
            max-width: 320px;
        }
        .toast-success { background: #2e7d32; color: white; }
        .toast-error { background: #c62828; color: white; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }

        .turno-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .turno-card:hover:not(.disabled) {
            border-color: #6a1b9a;
            background: #f5f3ff;
        }
        .turno-card.selected {
            border-color: #6a1b9a;
            background: #6a1b9a;
            color: white;
        }
        .turno-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f1f5f9;
        }

        /* ✅ Loading Overlay Animations */
        #loadingOverlay {
            animation: fadeIn 0.3s ease-in-out;
        }
        #loadingOverlay > div {
            animation: slideUp 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to { 
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        #loadingOverlay * {
            user-select: none;
            -webkit-user-select: none;
        }
    </style>   
</head>
<body class="bg-white text-slate-800 font-sans antialiased">
    
    @if(session('success'))
        <div class="toast toast-success">
            <div class="font-semibold">✓ Éxito</div>
            <div class="text-sm opacity-90">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="toast toast-error">
            <div class="font-semibold">✗ Error</div>
            <div class="text-sm opacity-90">{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="toast toast-error">
            <div class="font-semibold">✗ Error de validación</div>
            <div class="text-sm opacity-90">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        </div>
    @endif
    
    <nav class="bg-white/95 backdrop-blur-sm shadow-sm p-4 flex justify-between items-center sticky top-0 z-50 border-b border-slate-100">
        <h1 class="font-logo text-2xl font-bold text-[#6a1b9a]">Isa Villegas</h1>
        
        <div class="hidden md:flex space-x-6">
            <a href="#servicios" class="text-sm font-semibold text-[#6a1b9a] hover:text-[#00acc1] transition-colors">Servicios</a>
            <a href="#nosotros" class="text-sm font-semibold text-[#6a1b9a] hover:text-[#00acc1] transition-colors">Nosotros</a>
            <a href="#opiniones" class="text-sm font-semibold text-[#6a1b9a] hover:text-[#00acc1] transition-colors">Opiniones</a>
            <a href="#cita" class="text-sm font-semibold text-[#6a1b9a] hover:text-[#00acc1] transition-colors">Agendar Cita</a>
            <a href="{{ route('consulta.formulario') }}" class="text-sm font-semibold text-[#6a1b9a] hover:text-[#00acc1] transition-colors">Consultar Resultados</a>
        </div>
        
        <button class="md:hidden text-[#6a1b9a]" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </nav>
    
    <div id="mobileMenu" class="hidden md:hidden bg-white border-b border-slate-100 p-4 space-y-3">
        <a href="#servicios" class="block text-[#6a1b9a] font-medium">Servicios</a>
        <a href="#nosotros" class="block text-[#6a1b9a] font-medium">Nosotros</a>
        <a href="#opiniones" class="block text-[#6a1b9a] font-medium">Opiniones</a>
        <a href="#cita" class="block text-[#6a1b9a] font-medium">Agendar Cita</a>
    </div>

    <header class="py-20 px-6 text-center bg-gradient-to-b from-[#f5f3ff] to-white">
        <img src="{{ asset('img/logo.png') }}" 
             alt="logo_clinica" 
             class="h-40 w-auto object-contain mx-auto block mb-8">
        
        <h2 class="font-logo text-5xl md:text-6xl font-extrabold mb-2 text-[#6a1b9a]">
            Isa Villegas
        </h2>
        <p class="text-xl md:text-2xl font-semibold text-[#00acc1] mb-2 uppercase tracking-wide">
            FISIOTERAPIA Y KINESIOLOGÍA
        </p>
        <p class="font-carta text-4xl md:text-5xl text-[#6a1b9a] italic">
            "Movimiento es Salud"  
        </p>
        
        <a href="#cita" class="inline-block mt-8 bg-[#6a1b9a] text-white px-8 py-3 rounded-full font-semibold hover:bg-[#4a148c] transition-all shadow-lg hover:shadow-xl">
            Agendar Cita Gratis →
        </a>
    </header>

    <section id="servicios" class="reveal py-16 px-6 max-w-6xl mx-auto">
        <div class="bg-white p-10 rounded-3xl shadow-xl border border-slate-100">
            <div class="text-center mb-12">
                <h3 class="font-logo text-3xl md:text-4xl font-bold text-[#6a1b9a] mb-4">Nuestros Tratamientos</h3>
                <div class="h-1.5 w-24 bg-[#6a1b9a] mx-auto rounded-full"></div>
            </div>

            <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-6 gap-x-8 text-slate-700">
                <li class="flex items-start gap-4 p-4 rounded-2xl hover:bg-[#f5f3ff] transition-colors">
                    <span class="flex-shrink-0 w-10 h-10 rounded-full bg-[#00acc1]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00acc1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-semibold text-[#6a1b9a]">Terapia Manual</span>
                        <p class="text-sm text-slate-500">Técnicas especializadas para aliviar dolor y mejorar movilidad.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4 p-4 rounded-2xl hover:bg-[#f5f3ff] transition-colors">
                    <span class="flex-shrink-0 w-10 h-10 rounded-full bg-[#00acc1]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00acc1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-semibold text-[#6a1b9a]">Rehabilitación Deportiva</span>
                        <p class="text-sm text-slate-500">Recuperación optimizada para atletas y personas activas.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4 p-4 rounded-2xl hover:bg-[#f5f3ff] transition-colors">
                    <span class="flex-shrink-0 w-10 h-10 rounded-full bg-[#00acc1]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00acc1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-semibold text-[#6a1b9a]">Punción Seca</span>
                        <p class="text-sm text-slate-500">Alivio efectivo para puntos de dolor muscular y contracturas.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4 p-4 rounded-2xl hover:bg-[#f5f3ff] transition-colors">
                    <span class="flex-shrink-0 w-10 h-10 rounded-full bg-[#00acc1]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00acc1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-semibold text-[#6a1b9a]">Prevención de Lesiones</span>
                        <p class="text-sm text-slate-500">Programas personalizados para mantener tu cuerpo saludable.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4 p-4 rounded-2xl hover:bg-[#f5f3ff] transition-colors">
                    <span class="flex-shrink-0 w-10 h-10 rounded-full bg-[#00acc1]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00acc1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-semibold text-[#6a1b9a]">Ejercicios Terapéuticos</span>
                        <p class="text-sm text-slate-500">Rutinas guiadas para fortalecer y recuperar funcionalidad.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4 p-4 rounded-2xl hover:bg-[#f5f3ff] transition-colors">
                    <span class="flex-shrink-0 w-10 h-10 rounded-full bg-[#00acc1]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00acc1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-semibold text-[#6a1b9a]">Recuperación Post-quirúrgica</span>
                        <p class="text-sm text-slate-500">Acompañamiento especializado después de intervenciones.</p>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    <section class="reveal py-20 px-6 max-w-6xl mx-auto" id="nosotros">
        <div class="text-center mb-16">
            <h2 class="font-logo text-3xl md:text-4xl font-bold text-[#6a1b9a] mb-4">Sobre Nosotros</h2>
            <div class="h-1.5 w-24 bg-[#6a1b9a] mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <h3 class="text-2xl font-bold text-slate-800">Nuestra Clínica</h3>
                <p class="text-lg text-slate-600 leading-relaxed">
                    En Isa Villegas Fisioterapia, combinamos experiencia profesional con un enfoque humano y personalizado. 
                    Cada tratamiento está diseñado para ayudarte a recuperar tu bienestar y calidad de vida.
                </p>
                <p class="text-lg text-slate-600 leading-relaxed">
                    Contamos con equipamiento moderno y técnicas actualizadas para brindarte la mejor atención en fisioterapia y kinesiología.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-[#6a1b9a]">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-[#6a1b9a]/10 rounded-xl">
                            <svg class="w-6 h-6 text-[#6a1b9a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800">Misión</h4>
                    </div>
                    <p class="text-slate-600">
                        Brindar tratamientos de fisioterapia de excelencia, promoviendo la recuperación funcional 
                        y el bienestar integral de cada paciente.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-[#00acc1]">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-[#00acc1]/10 rounded-xl">
                            <svg class="w-6 h-6 text-[#00acc1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800">Visión</h4>
                    </div>
                    <p class="text-slate-600">
                        Ser referentes en fisioterapia en Sucre, reconocidos por nuestra calidad humana, 
                        innovación terapéutica y resultados comprobados.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="reveal py-20 px-6 max-w-6xl mx-auto bg-[#f5f3ff]/50">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1">
                <h2 class="font-logo text-3xl md:text-4xl font-bold text-[#6a1b9a] mb-4">Isabel Villegas García</h2>
                <p class="text-lg text-[#00acc1] font-semibold mb-4">Fisioterapeuta y Kinesióloga</p>
                <p class="text-slate-600 leading-relaxed mb-4">
                    Con más de 10 años de experiencia profesional, me especializo en tratamientos personalizados 
                    que combinan técnicas manuales, ejercicios terapéuticos y tecnología moderna.
                </p>
                <ul class="space-y-2 text-slate-600">
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#6a1b9a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Licenciada en Fisioterapia y Kinesiología
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#6a1b9a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Especialización en Rehabilitación Deportiva
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#6a1b9a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Formación continua en técnicas innovadoras
                    </li>
                </ul>
            </div>
            <div class="order-1 lg:order-2 flex justify-center">
                <div class="w-64 h-64 md:w-80 md:h-80 rounded-full bg-gradient-to-br from-[#6a1b9a] to-[#00acc1] flex items-center justify-center shadow-2xl">
                    <span class="text-white text-6xl font-carta">IV</span>
                </div>
            </div>
        </div>
    </section>
    
    <section class="reveal py-20 px-6 max-w-6xl mx-auto" id="opiniones">
        <div class="text-center mb-16">
            <h2 class="font-logo text-3xl md:text-4xl font-bold text-[#6a1b9a] mb-4">Opiniones de Nuestros Pacientes</h2>
            <div class="h-1.5 w-24 bg-[#6a1b9a] mx-auto rounded-full"></div>
            <p class="text-slate-600 mt-4 max-w-2xl mx-auto">
                La satisfacción de nuestros pacientes es nuestro mayor logro. Estas son algunas de sus experiencias.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#6a1b9a]/10 flex items-center justify-center text-[#6a1b9a] font-bold">
                        GP
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Gabrila P.</p>
                        <div class="stars">★★★★★</div>
                    </div>
                </div>
                <p class="text-slate-600 leading-relaxed">
                    "Después de mi lesión deportiva, pensé que no volvería a correr. Con el tratamiento de Isa, 
                    recuperé mi movilidad en pocas semanas. ¡Totalmente recomendada!"
                </p>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#00acc1]/10 flex items-center justify-center text-[#00acc1] font-bold">
                        JF
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Julian F.</p>
                        <div class="stars">★★★★★</div>
                    </div>
                </div>
                <p class="text-slate-600 leading-relaxed">
                    "El dolor de espalda que tenía por años desapareció. Isa es muy profesional y explica cada paso 
                    del tratamiento. Me siento mucho mejor."
                </p>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#6a1b9a]/10 flex items-center justify-center text-[#6a1b9a] font-bold">
                        AL
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Andrea L.</p>
                        <div class="stars">★★★★★</div>
                    </div>
                </div>
                <p class="text-slate-600 leading-relaxed">
                    "Excelente atención post-quirúrgica. Me ayudó a recuperar la fuerza en mi rodilla después de la cirugía. 
                    Muy agradecida por su dedicación."
                </p>
            </div>
        </div>
        
        <div class="text-center mt-10">
            <a href="#cita" class="inline-flex items-center gap-2 text-[#6a1b9a] font-semibold hover:text-[#4a148c] transition-colors">
                ¿Quieres ser el próximo paciente satisfecho?
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </section>

    <section class="reveal py-20 px-6 bg-gradient-to-b from-white to-[#f5f3ff]" id="cita">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="font-logo text-3xl md:text-4xl font-bold text-[#6a1b9a] mb-4">Agenda tu Cita</h2>
                <div class="h-1.5 w-24 bg-[#6a1b9a] mx-auto rounded-full"></div>
                <p class="text-slate-600 mt-4">
                    Completa el formulario y nos contactaremos contigo para confirmar tu horario.
                </p>
                <div class="mt-4 inline-flex items-center gap-2 bg-[#00acc1]/10 text-[#00acc1] px-4 py-2 rounded-full text-sm font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Solo 3 cupos disponibles por turno
                </div>
            </div>

            <form action="{{ route('citas.public.store') }}" method="POST" class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100" id="citaForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nombre Completo *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" 
                               class="form-input w-full px-4 py-3 rounded-xl @error('nombre') border-red-400 @enderror"
                               placeholder="Tu nombre completo" required>
                        @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Cédula de Identidad *</label>
                        <input type="text" name="ci" value="{{ old('ci') }}" 
                               class="form-input w-full px-4 py-3 rounded-xl @error('ci') border-red-400 @enderror"
                               placeholder="Ej: 1234567" required>
                        @error('ci') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Edad *</label>
                        <input type="number" name="edad" value="{{ old('edad') }}" min="1" max="120"
                               class="form-input w-full px-4 py-3 rounded-xl @error('edad') border-red-400 @enderror"
                               placeholder="Tu edad" required>
                        @error('edad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Teléfono *</label>
                        <input type="tel" name="telefono" value="{{ old('telefono') }}" 
                               class="form-input w-full px-4 py-3 rounded-xl @error('telefono') border-red-400 @enderror"
                               placeholder="Ej: +591 7XX XXXXX" required>
                        @error('telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Correo Electrónico *</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="form-input w-full px-4 py-3 rounded-xl @error('email') border-red-400 @enderror"
                               placeholder="tu@email.com" required>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Actividad Física *</label>
                        <select name="actividad_fisica" class="form-input w-full px-4 py-3 rounded-xl @error('actividad_fisica') border-red-400 @enderror" required>
                            <option value="">Selecciona una opción</option>
                            <option value="competencia" {{ old('actividad_fisica') == 'competencia' ? 'selected' : '' }}>🏆 Competencia Deportiva</option>
                            <option value="salud" {{ old('actividad_fisica') == 'salud' ? 'selected' : '' }}>💚 Salud / Bienestar</option>
                            <option value="ninguna" {{ old('actividad_fisica') == 'ninguna' ? 'selected' : '' }}>⚪ Ninguna</option>
                        </select>
                        @error('actividad_fisica') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Fecha Preferida *</label>
                        <input type="date" 
                               id="fecha_preferida"
                               name="fecha_preferida" 
                               value="{{ old('fecha_preferida') }}"
                               class="form-input w-full px-4 py-3 rounded-xl @error('fecha_preferida') border-red-400 @enderror"
                               min="{{ date('Y-m-d') }}" 
                               required>
                        @error('fecha_preferida') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Selecciona un Turno *</label>
                        <div id="turnos_container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="text-slate-500 text-sm italic">Primero selecciona una fecha</div>
                        </div>
                        <input type="hidden" name="turno" id="turno_seleccionado" required>
                        @error('turno') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-slate-500 mt-2" id="turno_ayuda"></p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Motivo de Consulta *</label>
                        <textarea name="motivo" rows="3" 
                                  class="form-input w-full px-4 py-3 rounded-xl @error('motivo') border-red-400 @enderror"
                                  placeholder="Describe brevemente tu consulta o lesión..." required>{{ old('motivo') }}</textarea>
                        @error('motivo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <button type="submit" 
                        id="btn_submit"
                        disabled
                        class="w-full bg-gray-400 text-white py-4 rounded-xl font-semibold text-lg transition-all shadow-lg flex items-center justify-center gap-2 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Selecciona un turno para continuar
                </button>
                
                <p class="text-center text-xs text-slate-500 mt-4">
                    🔒 Tus datos están protegidos. Solo los usaremos para contactarte sobre tu cita.
                </p>
            </form>
        </div>
    </section>

    <section class="reveal py-20 px-6 max-w-6xl mx-auto" id="contacto">
        <div class="text-center mb-16">
            <h2 class="font-logo text-3xl md:text-4xl font-bold text-[#6a1b9a] mb-4">Ubicación y Contacto</h2>
            <div class="h-1.5 w-24 bg-[#00acc1] mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-[#6a1b9a]">
                    <h3 class="text-2xl font-bold text-slate-800 mb-4">¿Dónde estamos?</h3>
                    <p class="text-lg text-slate-600 leading-relaxed flex items-start gap-3">
                        <span class="text-2xl">📍</span>
                        <span>
                            Calle Cornelio Duran N° 198<br>
                            <span class="font-semibold text-[#6a1b9a]">(Edificio 360)</span><br>
                            <small class="text-slate-400">Sucre, Bolivia</small>
                        </span>
                    </p>
                </div>
                
                <a href="https://maps.app.goo.gl/QSBFxfTG12NjfJSL6" target="_blank" 
                class="inline-flex items-center gap-2 bg-[#00acc1] text-white px-6 py-3 rounded-full font-bold hover:bg-[#008b9a] transition-all shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    Ver en Google Maps
                </a>
            </div>

            <div class="w-full h-80 lg:h-96 rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3771.551390492!2d-65.2504812!3d-19.0394334!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x93fbcf406f521191%3A0x600985a11c18090b!2sC.%20Cornelio%20Duran%20198%2C%20Sucre!5e0!3m2!1ses!2sbo!4v1714420000000!5m2!1ses!2sbo" 
                    class="w-full h-full border-0" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

    <!-- ✅ Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-2xl p-8 max-w-sm mx-4 text-center shadow-2xl transform transition-all">
            <div class="relative w-20 h-20 mx-auto mb-4">
                <div class="absolute inset-0 border-4 border-purple-100 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-[#6a1b9a] rounded-full border-t-transparent animate-spin"></div>
                <svg class="absolute inset-0 m-auto w-8 h-8 text-[#6a1b9a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2">Procesando tu solicitud</h3>
            <p class="text-gray-600 text-sm">
                Por favor espera mientras registramos tu cita. No cierres esta ventana.
            </p>
            
            <div class="mt-6 flex justify-center gap-2">
                <div class="w-2 h-2 bg-[#6a1b9a] rounded-full animate-bounce" style="animation-delay: 0s"></div>
                <div class="w-2 h-2 bg-[#6a1b9a] rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-[#6a1b9a] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("active");
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll(".reveal").forEach((el) => observer.observe(el));
            
            document.querySelectorAll('.toast').forEach(toast => {
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            });

            // ✅ Variables para loading overlay
            const form = document.getElementById('citaForm');
            const loadingOverlay = document.getElementById('loadingOverlay');
            let isSubmitting = false;

            // ✅ Prevenir múltiples envíos
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (isSubmitting) {
                        e.preventDefault();
                        return false;
                    }

                    const turnoSeleccionado = document.getElementById('turno_seleccionado');
                    if (!turnoSeleccionado || !turnoSeleccionado.value) {
                        e.preventDefault();
                        alert('Por favor selecciona un turno disponible');
                        return false;
                    }

                    isSubmitting = true;
                    loadingOverlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    
                    const btnSubmit = document.getElementById('btn_submit');
                    if (btnSubmit) {
                        btnSubmit.disabled = true;
                        btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
                    }

                    return true;
                });
            }


            @if($errors->any())
                if (loadingOverlay) {
                    loadingOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    isSubmitting = false;
                    const btn = document.getElementById('btn_submit');
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }
            @endif

            const fechaInput = document.getElementById('fecha_preferida');
            const turnosContainer = document.getElementById('turnos_container');
            const turnoInput = document.getElementById('turno_seleccionado');
            const turnoAyuda = document.getElementById('turno_ayuda');
            const btnSubmit = document.getElementById('btn_submit');

            const turnosConfig = {
                'manana': { 
                    nombre: '🌅 Mañana', 
                    horario: '8:00 - 12:00',
                    cupos_maximos: 3 
                },
                'tarde': { 
                    nombre: '🌆 Tarde', 
                    horario: '14:00 - 18:00',
                    cupos_maximos: 3 
                }
            };

            fechaInput.addEventListener('change', async function() {
                const fecha = this.value;
                if (!fecha) return;

                turnosContainer.innerHTML = '<div class="col-span-2 text-center text-slate-500 py-4">Cargando turnos disponibles...</div>';
                turnoInput.value = '';
                btnSubmit.disabled = true;
                btnSubmit.classList.remove('bg-[#6a1b9a]', 'hover:bg-[#4a148c]');
                btnSubmit.classList.add('bg-gray-400');
                btnSubmit.innerHTML = `
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Cargando...
                `;

                try {
                    const response = await fetch(`/api/turnos-disponibles?fecha=${fecha}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    renderTurnos(data);
                } catch (error) {
                    console.error('Error:', error);
                    turnosContainer.innerHTML = '<div class="col-span-2 text-center text-red-500 py-4">Error al cargar turnos. Inténtalo de nuevo.</div>';
                }
            });

            function renderTurnos(turnos) {
                turnosContainer.innerHTML = '';
                let hayDisponibles = false;

                for (const [key, turno] of Object.entries(turnos)) {
                    const config = turnosConfig[key];
                    const disponible = turno.disponible > 0;
                    
                    if (disponible) hayDisponibles = true;

                    const turnoCard = document.createElement('div');
                    turnoCard.className = `turno-card ${!disponible ? 'disabled' : ''}`;
                    turnoCard.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-bold text-lg">${config.nombre}</div>
                                <div class="text-sm text-slate-600">${config.horario}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold ${disponible ? 'text-green-600' : 'text-red-600'}">
                                    ${turno.disponible}
                                </div>
                                <div class="text-xs text-slate-500">cupos</div>
                            </div>
                        </div>
                        ${!disponible ? '<div class="mt-2 text-sm text-red-600 font-semibold">⛔ Sin cupos disponibles</div>' : ''}
                    `;

                    if (disponible) {
                        turnoCard.addEventListener('click', () => selectTurno(key, turnoCard));
                    }

                    turnosContainer.appendChild(turnoCard);
                }

                if (!hayDisponibles) {
                    turnoAyuda.innerHTML = '<span class="text-red-600 font-semibold">⚠️ No hay turnos disponibles para esta fecha. Por favor selecciona otra fecha.</span>';
                } else {
                    turnoAyuda.innerHTML = '<span class="text-green-600">✅ Haz clic en un turno disponible para seleccionarlo</span>';
                }
            }

            function selectTurno(turnoKey, cardElement) {
                document.querySelectorAll('.turno-card').forEach(card => card.classList.remove('selected'));
                cardElement.classList.add('selected');
                turnoInput.value = turnoKey;
                
                const config = turnosConfig[turnoKey];
                
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('bg-gray-400', 'cursor-not-allowed');
                btnSubmit.classList.add('bg-[#6a1b9a]', 'hover:bg-[#4a148c]');
                btnSubmit.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Solicitar Cita - Turno ${config.nombre}
                `;
                
                turnoAyuda.innerHTML = `<span class="text-[#6a1b9a] font-semibold">✅ Turno ${config.nombre} (${config.horario}) seleccionado</span>`;
            }
        });
    </script>

</body>
</html>