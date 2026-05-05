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
        .summary { margin: 20px 0; }
        .summary-item { display: flex; justify-content: space-between; padding: 10px; margin: 5px 0; background: #f5f5f5; border-radius: 5px; }
        .summary-total { background: #6a1b9a; color: white; font-weight: bold; font-size: 12pt; }
        .footer { margin-top: 30px; text-align: center; font-size: 9pt; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
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

    <!-- Resumen -->
    <div class="summary">
        <div class="summary-item">
            <span>📈 Total Ingresos:</span>
            <span class="text-success">Bs {{ number_format($total_ingresos, 2) }}</span>
        </div>
        <div class="summary-item">
            <span>📉 Total Egresos:</span>
            <span class="text-danger">Bs {{ number_format($total_egresos, 2) }}</span>
        </div>
        <div class="summary-item summary-total">
            <span>💰 Balance Neto:</span>
            <span>Bs {{ number_format($balance, 2) }}</span>
        </div>
    </div>

    <!-- Ingresos -->
    <h3 style="color: #2e7d32; border-bottom: 2px solid #2e7d32; padding-bottom: 5px;">📈 Ingresos</h3>
    @if($ingresos->isEmpty())
        <p style="color: #666; font-style: italic;">No hay ingresos registrados en este período.</p>
    @else
    <table>
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

    <!-- Egresos -->
    <h3 style="color: #c62828; border-bottom: 2px solid #c62828; padding-bottom: 5px; margin-top: 30px;">📉 Egresos</h3>
    @if($egresos->isEmpty())
        <p style="color: #666; font-style: italic;">No hay egresos registrados en este período.</p>
    @else
    <table>
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
        <p>© {{ date('Y') }} Isa Villegas - Reporte financiero generado automáticamente</p>
    </div>
</body>
</html>