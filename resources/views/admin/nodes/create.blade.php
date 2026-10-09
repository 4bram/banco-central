@extends('layouts.admin')

@section('title', 'Crear Nodo')
@section('content')

<div class="card" style="max-width: 600px;">
    <form method="POST" action="{{ route('admin.nodes.store') }}">
        @csrf

        <div class="form-group">
            <label>Nombre del nodo *</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Sucursal Centro">
        </div>

        <div class="form-group">
            <label>Tipo *</label>
            <select name="type" required>
                <option value="">— Selecciona —</option>
                <option value="branch" {{ old('type') === 'branch' ? 'selected' : '' }}>Sucursal</option>
                <option value="atm" {{ old('type') === 'atm' ? 'selected' : '' }}>Cajero ATM</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ubicación</label>
            <input type="text" name="location" value="{{ old('location') }}" placeholder="Av. Juárez 123, CDMX">
        </div>

        <div class="form-group">
            <label>Efectivo inicial</label>
            <input type="number" name="cash_available" value="{{ old('cash_available', 0) }}" min="0" step="0.01">
        </div>

        <h3 style="margin: 25px 0 15px; font-size: 16px;">Responsable (opcional)</h3>

        <div class="form-group">
            <label>Nombre completo</label>
            <input type="text" name="responsible[full_name]" value="{{ old('responsible.full_name') }}">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="responsible[email]" value="{{ old('responsible.email') }}">
        </div>

        <div style="display: flex; gap: 10px; margin-top: 25px;">
            <button type="submit" class="btn btn-success">Crear Nodo</button>
            <a href="{{ route('admin.nodes.index') }}" class="btn" style="background: #6b7280;">Cancelar</a>
        </div>
    </form>
</div>

@endsection