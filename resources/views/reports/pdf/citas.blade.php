<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #6a1b9a; padding-bottom: 10px; }
        .header h1 { color: #6a1b9a; margin: 0; font-size: 18pt; }
        .header p { margin: 5px 0; color: #666; }
        .periodo { text-align: center; background: #f5f3ff; padding: 10px; margin: 15px 0; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #6a1b9a; color: white; padding: 8px; text-align: left; font-weight: bold; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-success { color: #2e7d32; font-weight: bold; }
        .text-danger { color: #c62828; font-weight: bold; }
        .text-warning { color: #f57f17; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 9pt; font-weight: bold; }
        .badge-pendiente { background: #fff3e0; color: #e65100; }
        .badge-realizado { background: #e8f5e9; color: #1b5e20; }
        .badge-cancelado { background: #ffebee; color: #b71c1c; }
        .summary { margin: 30px 0; page-break-inside: avoid; }
        .summary-item { display: flex; justify-content: space-between; padding: 10px; margin: 5px 0; background: #f5f5f5; border-radius: 5px; }
        .summary-total { background: #6a1b9a; color: white; font-weight: bold; font-size: 12pt; }
        .footer { margin-top: 30px; text-align: center; font-size: 9pt; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
        .estadisticas { display: flex; justify-content: space-around; margin: 20px 0; }
        .estadistica-box { text-align: center; padding: 15px; background: #f5f3ff; border-radius: 8px; min-width: 120px; }
        .estadistica-number { font-size: 24pt; font-weight: bold; color: #6a1b9a; }
        .estadistica-label { font-size: 9pt; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Isa Villegas - Fisioterapia y Kinesiología</p>
        <p>Generado: {{ $fecha_generacion }}</p>
    </div>

    @if($periodo)
    <div class="periodo">
        <strong>Período:</strong> {{ $periodo }}
    </div>
    @endif

    {{-- ✅ ESTADÍSTICAS --}}
    <div class="estadisticas">
        <div class="estadistica-box">
            <div class="estadistica-number">{{ $estadisticas['total'] }}</div>
            <div class="estadistica-label">Total Citas</div>
        </div>
        <div class="estadistica-box">
            <div class="estadistica-number">{{ $estadisticas['pendiente'] }}</div>
            <div class="estadistica-label">Pendientes</div>
        </div>
        <div class="estadistica-box">
            <div class="estadistica-number">{{ $estadisticas['realizado'] }}</div>
            <div class="estadistica-label">Realizadas</div>
        </div>
        <div class="estadistica-box">
            <div class="estadistica-number">{{ $estadisticas['cancelado'] }}</div>
            <div class="estadistica-label">Canceladas</div>
        </div>
    </div>

    {{-- ✅ LISTA DE CITAS - LO PRINCIPAL --}}
    <h2 style="color: #6a1b9a; border-bottom: 2px solid #6a1b9a; padding-bottom: 5px; margin-top: 30px;">📅 Citas del Período</h2>
    
    @if($citas->isEmpty())
        <p style="color: #666; font-style: italic; text-align: center; padding: 20px;">No hay citas registradas en este período.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Paciente</th>
                <th>Estado</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($citas as $cita)
            <tr>
                <td>{{ \Carbon\Carbon::parse($cita->fecha_hora)->format('d/m/Y H:i') }}</td>
                <td>{{ $cita->paciente->nombre ?? 'N/A' }}</td>
                <td>
                    <span class="badge 
                        @if($cita->estado === 'pendiente') badge-pendiente
                        @elseif($cita->estado === 'realizado') badge-realizado
                        @else badge-cancelado
                        @endif">
                        @if($cita->estado === 'pendiente') ⏳ Pendiente
                        @elseif($cita->estado === 'realizado') ✅ Realizado
                        @else ❌ Cancelado
                        @endif
                    </span>
                </td>
                <td>{{ Str::limit($cita->motivo, 50) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ✅ RESUMEN FINANCIERO (OPCIONAL - al final) --}}
    <div class="summary">
        <h3 style="color: #6a1b9a; border-bottom: 1px solid #6a1b9a; padding-bottom: 5px;">💰 Resumen Financiero del Período</h3>
        <div class="summary-item">
            <span>📈 Total Ingresos:</span>
            <span class="text-success">Bs {{ number_format($total_ingresos, 2) }}</span>
        </div>
        <div class="summary-item">
            <span>📉 Total Egresos:</span>
            <span class="text-danger">Bs {{ number_format($total_egresos, 2) }}</span>
        </div>
        <div class="summary-item summary-total">
            <span>💵 Balance Neto:</span>
            <span>Bs {{ number_format($balance, 2) }}</span>
        </div>
    </div>

    {{-- Detalles de ingresos (opcional, si quieres mostrarlos) --}}
    @if(isset($ingresos) && $ingresos->isNotEmpty())
    <h4 style="color: #2e7d32; margin-top: 20px;">📈 Detalle de Ingresos</h4>
    <table style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingresos as $ing)
            <tr>
                <td>{{ \Carbon\Carbon::parse($ing->fecha)->format('d/m/Y') }}</td>
                <td>{{ $ing->descripcion }}</td>
                <td class="text-right text-success">+ Bs {{ number_format($ing->monto, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Detalles de egresos (opcional, si quieres mostrarlos) --}}
    @if(isset($egresos) && $egresos->isNotEmpty())
    <h4 style="color: #c62828; margin-top: 20px;">📉 Detalle de Egresos</h4>
    <table style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($egresos as $egr)
            <tr>
                <td>{{ \Carbon\Carbon::parse($egr->fecha)->format('d/m/Y') }}</td>
                <td>{{ $egr->descripcion }}</td>
                <td class="text-right text-danger">- Bs {{ number_format($egr->monto, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>© {{ date('Y') }} Isa Villegas - Reporte de Citas generado automáticamente</p>
    </div>
</body>
</html>