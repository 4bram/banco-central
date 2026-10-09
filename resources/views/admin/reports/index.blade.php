@extends('layouts.admin')
@section('title', 'Reportes')
@section('content')

<h3 style="margin-bottom: 15px;">Transacciones por tipo</h3>
<table style="margin-bottom: 30px;">
    <thead><tr><th>Tipo</th><th>Total</th><th>Monto acumulado</th></tr></thead>
    <tbody>
        @forelse($byType as $r)
        <tr>
            <td><span class="badge badge-{{ $r->type === 'withdrawal' ? 'inactive' : ($r->type === 'deposit' ? 'active' : 'branch') }}">{{ $r->type }}</span></td>
            <td>{{ $r->total }}</td>
            <td>${{ number_format($r->monto, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="3" style="text-align: center; color: #9ca3af;">Sin datos</td></tr>
        @endforelse
    </tbody>
</table>

<h3 style="margin-bottom: 15px;">Transacciones por nodo</h3>
<table style="margin-bottom: 30px;">
    <thead><tr><th>Nodo</th><th>Tipo</th><th>Total transacciones</th><th>Monto acumulado</th></tr></thead>
    <tbody>
        @forelse($byNode as $r)
        <tr>
            <td>{{ $r->node->name ?? '—' }}</td>
            <td>{{ $r->node->type ?? '—' }}</td>
            <td>{{ $r->total }}</td>
            <td>${{ number_format($r->monto, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align: center; color: #9ca3af;">Sin datos</td></tr>
        @endforelse
    </tbody>
</table>

<h3 style="margin-bottom: 15px;">Efectivo disponible por nodo</h3>
<table>
    <thead><tr><th>Nodo</th><th>Tipo</th><th>Efectivo</th></tr></thead>
    <tbody>
        @foreach($cashPerNode as $n)
        <tr>
            <td>{{ $n->name }}</td>
            <td><span class="badge badge-{{ $n->type }}">{{ $n->type }}</span></td>
            <td>${{ number_format($n->cash_available, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
