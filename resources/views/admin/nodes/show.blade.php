@extends('layouts.admin')

@section('title', $node->name)
@section('content')

@if(session('new_api_key'))
<div class="alert alert-info">
    <strong>API Key generada (solo se muestra una vez):</strong><br>
    <code style="background: #fff; padding: 8px; display: inline-block; margin-top: 8px; border-radius: 4px; font-size: 13px; word-break: break-all;">{{ session('new_api_key') }}</code>
    <br><small>Copiala ahora. No se puede recuperar despues.</small>
</div>
@endif

<div class="grid">
    <div class="stat">
        <div class="label">Tipo</div>
        <div class="value" style="font-size: 20px;"><span class="badge badge-{{ $node->type }}">{{ $node->type }}</span></div>
    </div>
    <div class="stat">
        <div class="label">Efectivo disponible</div>
        <div class="value" style="font-size: 24px;">${{ number_format($node->cash_available, 2) }}</div>
    </div>
    <div class="stat">
        <div class="label">Estado</div>
        <div class="value" style="font-size: 20px;"><span class="badge badge-{{ $node->status === 'active' ? 'active' : 'inactive' }}">{{ $node->status }}</span></div>
    </div>
    <div class="stat">
        <div class="label">Transacciones</div>
        <div class="value">{{ $node->transactions->count() }}</div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 15px;">Información</h3>
    <table>
        <tr><td style="width: 200px; color: #6b7280;">ID</td><td><code>{{ $node->id }}</code></td></tr>
        <tr><td style="color: #6b7280;">Ubicación</td><td>{{ $node->location ?? '—' }}</td></tr>
        <tr><td style="color: #6b7280;">Creado</td><td>{{ $node->created_at->format('d/m/Y H:i') }}</td></tr>
    </table>
</div>

<h3 style="margin: 30px 0 15px;">Ajustar efectivo</h3>
<div class="card">
    <form method="POST" action="{{ route('admin.nodes.cash', $node->id) }}">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label>Tipo de movimiento</label>
                <select name="type" required>
                    <option value="replenishment">Reabastecimiento (+)</option>
                    <option value="allocation">Asignación (+)</option>
                    <option value="adjustment">Ajuste (±)</option>
                </select>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Monto</label>
                <input type="number" name="delta" step="0.01" required placeholder="50000">
            </div>
            <button type="submit" class="btn btn-success">Ajustar</button>
        </div>
    </form>
</div>

<h3 style="margin: 30px 0 15px;">Responsables</h3>
<table>
    <thead>
        <tr><th>Nombre</th><th>Email</th><th>Asignado</th><th>Activo</th></tr>
    </thead>
    <tbody>
        @forelse($node->responsibles as $r)
        <tr>
            <td>{{ $r->full_name }}</td>
            <td>{{ $r->email }}</td>
            <td>{{ $r->assigned_at->format('d/m/Y') }}</td>
            <td><span class="badge badge-{{ $r->is_active ? 'active' : 'inactive' }}">{{ $r->is_active ? 'Sí' : 'No' }}</span></td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align: center; color: #9ca3af;">Sin responsables</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 30px;">
    <a href="{{ route('admin.nodes.index') }}" class="btn" style="background: #6b7280;">← Volver</a>
</div>

@endsection