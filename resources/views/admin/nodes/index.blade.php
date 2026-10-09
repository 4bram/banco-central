@extends('layouts.admin')

@section('title', 'Nodos')
@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <p style="color: #6b7280;">Total: {{ $nodes->count() }} nodos</p>
    <a href="{{ route('admin.nodes.create') }}" class="btn">+ Nuevo Nodo</a>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Ubicación</th>
            <th>Efectivo</th>
            <th>Estado</th>
            <th>Creado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($nodes as $node)
        <tr>
            <td><strong>{{ $node->name }}</strong></td>
            <td><span class="badge badge-{{ $node->type }}">{{ $node->type }}</span></td>
            <td>{{ $node->location ?? '—' }}</td>
            <td>${{ number_format($node->cash_available, 2) }}</td>
            <td><span class="badge badge-{{ $node->status === 'active' ? 'active' : 'inactive' }}">{{ $node->status }}</span></td>
            <td>{{ $node->created_at->format('d/m/Y H:i') }}</td>
            <td><a href="{{ route('admin.nodes.show', $node->id) }}" class="btn">Ver</a></td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align: center; color: #9ca3af;">Sin nodos registrados</td></tr>
        @endforelse
    </tbody>
</table>

@endsection