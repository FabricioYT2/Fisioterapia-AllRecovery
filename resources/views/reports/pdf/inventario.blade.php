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
        .alert { padding: 10px; margin: 15px 0; border-radius: 8px; }
        .alert-warning { background: #fff3e0; border-left: 4px solid #ffb74d; color: #e65100; }
        .alert-danger { background: #ffebee; border-left: 4px solid #e57373; color: #c62828; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #6a1b9a; color: white; padding: 8px; text-align: left; font-weight: bold; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .stock-ok { color: #2e7d32; font-weight: bold; }
        .stock-bajo { color: #e65100; font-weight: bold; }
        .stock-agotado { color: #c62828; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 9pt; }
        .badge-success { background: #e8f5e9; color: #2e7d32; }
        .badge-warning { background: #fff3e0; color: #e65100; }
        .badge-danger { background: #ffebee; color: #c62828; }
        .footer { margin-top: 30px; text-align: center; font-size: 9pt; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Isa Villegas - Fisioterapia y Kinesiología</p>
        <p>Generado: {{ $fecha_generacion }}</p>
    </div>

    <!-- Alertas de stock -->
    @if($stock_bajo > 0)
    <div class="alert alert-warning">
        ⚠️ <strong>Atención:</strong> {{ $stock_bajo }} material(es) con stock bajo (≤ mínimo). ¡Reabastecer pronto!
    </div>
    @endif
    @if($agotados > 0)
    <div class="alert alert-danger">
        🔴 <strong>Crítico:</strong> {{ $agotados }} material(es) AGOTADOS. ¡Comprar urgentemente!
    </div>
    @endif

    <!-- Tabla de inventario -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Material</th>
                <th>Unidad</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materiales as $index => $m)
            @php
                $estado = $m->stock_actual == 0 ? 'agotado' : ($m->stock_actual <= $m->stock_minimo ? 'bajo' : 'ok');
                $clase = $estado == 'agotado' ? 'stock-agotado' : ($estado == 'bajo' ? 'stock-bajo' : 'stock-ok');
                $badge = $estado == 'agotado' ? 'badge-danger' : ($estado == 'bajo' ? 'badge-warning' : 'badge-success');
                $texto = $estado == 'agotado' ? '🔴 Agotado' : ($estado == 'bajo' ? '⚠️ Bajo' : '✅ Normal');
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $m->nombre }}</td>
                <td>{{ $m->unidad }}</td>
                <td class="{{ $clase }}">{{ $m->stock_actual }}</td>
                <td>{{ $m->stock_minimo }}</td>
                <td><span class="badge {{ $badge }}">{{ $texto }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Resumen -->
    <div style="margin-top: 30px; padding: 15px; background: #f5f5f5; border-radius: 8px;">
        <p style="margin: 5px 0;"><strong>Total de materiales:</strong> {{ $materiales->count() }}</p>
        <p style="margin: 5px 0;"><strong>✅ Stock normal:</strong> {{ $materiales->filter(fn($m) => $m->stock_actual > $m->stock_minimo)->count() }}</p>
        <p style="margin: 5px 0;"><strong>⚠️ Stock bajo:</strong> {{ $stock_bajo }}</p>
        <p style="margin: 5px 0;"><strong>🔴 Agotados:</strong> {{ $agotados }}</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Isa Villegas - Inventario generado automáticamente</p>
    </div>
</body>
</html>