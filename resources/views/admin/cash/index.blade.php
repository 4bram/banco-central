@extends('layouts.admin')
@section('title', 'Movimientos de efectivo')
@section('content')

<div class="card">
    <form method="GET" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="margin: 0; flex: 1;">
            <label>Nodo</label>
            <select name="node_id">
                <option value="">— Todos —</option>
                @foreach($nodes as $n)
                    <option value="{{ $n->id }}" {{ request('node_id') === $n->id ? 'selected' : '' }}>
                        {{ $n->name }} ({{ $n->type }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin: 0; flex: 1;">
            <label>Tipo</label>
            <select name="type">
                <option value="">— Todos —</option>
                <option value="allocation"    {{ request('type') === 'allocation' ? 'selected' : '' }}>Asignación</option>
                <option value="replenishment" {{ request('type') === 'replenishment' ? 'selected' : '' }}>Reabastecimiento</option>
                <option value="adjustment"    {{ request('type') === 'adjustment' ? 'selected' : '' }}>Ajuste</option>
            </select>
        </div>
        <button type="submit" class="btn">Filtrar</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Nodo</th>
            <th>Tipo</th>
            <th>Monto</th>
            <th>Efectivo antes</th>
            <th>Efectivo después</th>
        </tr>
    </thead>
    <tbody>
        @forelse($movements as $m)
        <tr>
            <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $m->node->name ?? '—' }}</td>
            <td>
                <span class="badge badge-{{ $m->type === 'replenishment' ? 'active' : ($m->type === 'allocation' ? 'branch' : 'atm') }}">
                    {{ $m->type }}
                </span>
            </td>
            <td>${{ number_format($m->amount, 2) }}</td>
            <td>${{ number_format($m->previous_cash, 2) }}</td>
            <td>${{ number_format($m->new_cash, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align: center; color: #9ca3af;">Sin movimientos registrados</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 20px;">{{ $movements->appends(request()->query())->links() }}</div>

@endsection