@extends('layouts.admin')

@section('title', 'Dashboard')
@section('content')

<div class="grid">
    <div class="stat">
        <div class="label">Sucursales</div>
        <div class="value">{{ $stats['branches'] }}</div>
    </div>
    <div class="stat">
        <div class="label">Cajeros ATM</div>
        <div class="value">{{ $stats['atms'] }}</div>
    </div>
    <div class="stat">
        <div class="label">Cuentas</div>
        <div class="value">{{ $stats['accounts'] }}</div>
    </div>
    <div class="stat">
        <div class="label">Transacciones</div>
        <div class="value">{{ $stats['transactions'] }}</div>
    </div>
    <div class="stat">
        <div class="label">Efectivo total</div>
        <div class="value">${{ number_format($stats['cash_total'], 2) }}</div>
    </div>
</div>

<h2 style="font-size: 18px; margin-bottom: 15px;">Últimas transacciones</h2>
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
        @forelse($recentTransactions as $tx)
        <tr>
            <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
            <td><span class="badge badge-{{ $tx->type === 'withdrawal' ? 'inactive' : ($tx->type === 'deposit' ? 'active' : 'branch') }}">{{ $tx->type }}</span></td>
            <td>${{ number_format($tx->amount, 2) }}</td>
            <td>{{ $tx->node->name ?? '—' }}</td>
            <td>{{ $tx->originAccount->account_number ?? '—' }}</td>
            <td>{{ $tx->destinationAccount->account_number ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align: center; color: #9ca3af;">Sin transacciones registradas</td></tr>
        @endforelse
    </tbody>
</table>

@endsection