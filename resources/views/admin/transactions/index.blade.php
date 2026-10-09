@extends('layouts.admin')
@section('title', 'Transacciones')
@section('content')

<div class="card">
    <form method="GET" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="margin: 0; flex: 1;">
            <label>Tipo</label>
            <select name="type">
                <option value="">— Todos —</option>
                <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Retiro</option>
                <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Depósito</option>
                <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transferencia</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0; flex: 1;">
            <label>Nodo</label>
            <select name="node_id">
                <option value="">— Todos —</option>
                @foreach($nodes as $n)
                    <option value="{{ $n->id }}" {{ request('node_id') === $n->id ? 'selected' : '' }}>{{ $n->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn">Filtrar</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Monto</th>
            <th>Nodo</th>
            <th>Origen</th>
            <th>Destino</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $tx)
        <tr>
            <td>{{ $tx->created_at->format('d/m/Y H:i:s') }}</td>
            <td><span class="badge badge-{{ $tx->type === 'withdrawal' ? 'inactive' : ($tx->type === 'deposit' ? 'active' : 'branch') }}">{{ $tx->type }}</span></td>
            <td>${{ number_format($tx->amount, 2) }}</td>
            <td>{{ $tx->node->name ?? '—' }}</td>
            <td>{{ $tx->originAccount->account_number ?? '—' }}</td>
            <td>{{ $tx->destinationAccount->account_number ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align: center; color: #9ca3af;">Sin transacciones</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 20px;">{{ $transactions->appends(request()->query())->links() }}</div>

@endsection
