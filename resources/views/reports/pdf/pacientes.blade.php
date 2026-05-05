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
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #6a1b9a; color: white; padding: 8px; text-align: left; font-weight: bold; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; font-size: 9pt; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 9pt; font-weight: bold; }
        .badge-info { background: #00acc1; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Isa Villegas - Fisioterapia y Kinesiología</p>
        <p>Generado: {{ $fecha_generacion }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>C.I.</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Actividad</th>
                <th>Citas</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pacientes as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->nombre }}</td>
                <td>{{ $p->ci }}</td>
                <td>{{ $p->telefono }}</td>
                <td>{{ $p->email }}</td>
                <td>
                    <span class="badge badge-info">
                        {{ $p->actividad_fisica === 'competencia' ? '🏆 Competencia' : ($p->actividad_fisica === 'salud' ? '💚 Salud' : '⚪ Ninguna') }}
                    </span>
                </td>
                <td style="text-align: center;">{{ $p->citas_count }}</td>
                <td>{{ \Carbon\Carbon::parse($p->fecha_registro)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Total de pacientes:</strong> {{ $pacientes->count() }}</p>
        <p>© {{ date('Y') }} Isa Villegas - Todos los derechos reservados</p>
    </div>
</body>
</html>