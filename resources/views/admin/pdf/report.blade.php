<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Reporte' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #111827;
            padding: 30px;
        }
        .header {
            border-bottom: 3px solid #1e293b;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 22px;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 12px;
            color: #6b7280;
        }
        .meta {
            font-size: 10px;
            color: #6b7280;
            margin-top: 10px;
            text-align: right;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            font-size: 14px;
            color: #1e293b;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background: #f3f4f6;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            color: #374151;
            border: 1px solid #e5e7eb;
            text-transform: uppercase;
        }
        table td {
            padding: 7px 10px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        table tr:nth-child(even) {
            background: #f9fafb;
        }
        .totals {
            font-weight: bold;
            background: #e5e7eb !important;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
        }
        .no-data {
            text-align: center;
            color: #9ca3af;
            padding: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>🏦 Banco Central</h1>
    <div class="subtitle">Sistema Bancario Distribuido</div>
    <div class="meta">
        Generado: {{ $generated_at->format('d/m/Y H:i:s') }}<br>
        Por: {{ $generated_by }}
    </div>
</div>

<div class="section">
    <h2>{{ $title ?? 'Reporte' }}</h2>

    @if(isset($subtitle))
        <p style="font-size: 11px; color: #6b7280; margin-bottom: 15px;">{{ $subtitle }}</p>
    @endif

    @if(count($rows) === 0)
        <div class="no-data">Sin datos para mostrar</div>
    @else
        <table>
            <thead>
                <tr>
                    @foreach($headers as $h)
                        <th>{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="footer">
    Banco Central © {{ date('Y') }} — Documento generado automáticamente
</div>

</body>
</html>