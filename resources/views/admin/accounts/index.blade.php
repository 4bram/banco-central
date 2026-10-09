@extends('layouts.admin')
@section('title', 'Cuentas')
@section('content')

<table>
    <thead>
        <tr>
            <th># Cuenta</th>
            <th>Titular</th>
            <th>Saldo</th>
            <th>Estado</th>
            <th>Creada por</th>
            <th>Fecha</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($accounts as $a)
        <tr>
            <td><code>{{ $a->account_number }}</code></td>
            <td>{{ $a->holder_name }}</td>
            <td>${{ number_format($a->balance, 2) }}</td>
            <td><span class="badge badge-{{ $a->status === 'active' ? 'active' : 'inactive' }}">{{ $a->status }}</span></td>
            <td>{{ $a->createdByNode->name ?? '—' }}</td>
            <td>{{ $a->created_at->format('d/m/Y') }}</td>
            <td><a href="{{ route('admin.accounts.show', $a->id) }}" class="btn">Ver</a></td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align: center; color: #9ca3af;">Sin cuentas registradas</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 20px;">{{ $accounts->links() }}</div>

@endsection