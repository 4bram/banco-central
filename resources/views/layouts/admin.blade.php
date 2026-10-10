<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin') — Banco Central</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f3f4f6;
            color: #111827;
            min-height: 100vh;
        }
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 240px;
            height: 100vh;
            background: #1e293b;
            color: #e2e8f0;
            padding: 20px 0;
            overflow-y: auto;
        }
        .sidebar .brand {
            padding: 0 20px 20px;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid #334155;
        }
        .sidebar .brand small {
            display: block;
            font-size: 12px;
            font-weight: 400;
            color: #94a3b8;
            margin-top: 4px;
        }
        .sidebar nav { padding: 20px 0; }
        .sidebar nav a {
            display: block;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14px;
            transition: all .15s;
            border-left: 3px solid transparent;
        }
        .sidebar nav a:hover {
            background: #334155;
            color: #fff;
        }
        .sidebar nav a.active {
            background: #334155;
            color: #fff;
            border-left-color: #3b82f6;
        }
        .main {
            margin-left: 240px;
            padding: 30px 40px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            margin-bottom: 30px;
            border-bottom: 1px solid #e5e7eb;
        }
        .topbar h1 {
            font-size: 24px;
            font-weight: 700;
        }
        .topbar .user {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
            color: #6b7280;
        }
        .topbar .user a {
            color: #dc2626;
            text-decoration: none;
            font-weight: 500;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            margin-bottom: 20px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }
        .stat .label {
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 8px;
        }
        .stat .value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }
        table thead {
            background: #f9fafb;
        }
        table th {
            text-align: left;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        table td {
            padding: 14px 16px;
            border-top: 1px solid #f3f4f6;
            font-size: 14px;
        }
        table tr:hover {
            background: #f9fafb;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #3b82f6;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background .15s;
        }
        .btn:hover { background: #2563eb; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-success { background: #16a34a; }
        .btn-success:hover { background: #15803d; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .badge-branch { background: #dbeafe; color: #1e40af; }
        .badge-atm { background: #fef3c7; color: #92400e; }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.1);
        }
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand">
        🏦 Banco Central
        <small>Panel Administrativo</small>
    </div>
    <nav>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
        <a href="{{ route('admin.nodes.index') }}" class="{{ request()->routeIs('admin.nodes.*') ? 'active' : '' }}">🏢 Nodos</a>
        <a href="{{ route('admin.cash.index') }}" class="{{ request()->routeIs('admin.cash.*') ? 'active' : '' }}">💵 Movimientos de efectivo</a>
        <a href="{{ route('admin.accounts.index') }}" class="{{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">💳 Cuentas</a>
        <a href="{{ route('admin.transactions.index') }}" class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">💸 Transacciones</a>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">📈 Reportes</a>
    </nav>
</aside>

<main class="main">
    <div class="topbar">
        <h1>@yield('title', 'Dashboard')</h1>
        <div class="user">
            <span>{{ session('admin.full_name') ?? 'Admin' }}</span>
            <a href="{{ route('admin.logout') }}">Cerrar sesión</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 18px;">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

</body>
</html>