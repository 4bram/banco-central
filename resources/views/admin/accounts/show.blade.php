@extends('layouts.admin')
@section('title', 'Cuenta ' . $account->account_number)
@section('content')

<div class="grid">
    <div class="stat">
        <div class="label">Saldo actual</div>
        <div class="value">${{ number_format($account->balance, 2) }}</div>
    </div>
    <div class="stat">
        <div class="label">Estado</div>
        <div class="value" style="font-size: 20px;"><span class="badge badge-{{ $account->status === 'active' ? 'active' : 'inactive' }}">{{ $account->status }}</span></div>
    </div>
    <div class="stat">
        <div class="label">Titular</div>
        <div class="value" style="font-size: 18px;">{{ $account->holder_name }}</div>
    </div>
</div>

<div class="card">
    <table>
        <tr><td style="color:#6b7280; width:200px;">ID</td><td><code>{{ $account->id }}</code></td></tr>
        <tr><td style="color:#6b7280;"># Cuenta</td><td><code>{{ $account->account_number }}</code></td></tr>
        <tr><td style="color:#6b7280;">Creada por</td><td>{{ $account->createdByNode->name ?? '—' }}</td></tr>
        <tr><td style="color:#6b7280;">Fecha de alta</td><td>{{ $account->created_at->format('d/m/Y H:i') }}</td></tr>
    </table>
</div>

<a href="{{ route('admin.accounts.index') }}" class="btn" style="background: #6b7280;">← Volver</a>

@endsection
