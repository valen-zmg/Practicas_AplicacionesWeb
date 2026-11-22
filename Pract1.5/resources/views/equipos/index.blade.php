@extends('layouts.app')
@section('titulo', 'Equipos')
@section('contenido')
@php($esAdmin = auth()->user()->esAdmin())

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">{{ $esAdmin ? 'Todos los equipos' : 'Equipos disponibles' }}</h1>
    @if($esAdmin)
        <a href="{{ route('equipos.create') }}" class="btn btn-primary">Nuevo equipo</a>
    @endif
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Código</th><th>Nombre</th><th>Descripción</th><th>Estado</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            @forelse($equipos as $e)
                <tr>
                    <td>{{ $e->codigo }}</td>
                    <td>{{ $e->nombre }}</td>
                    <td class="small text-muted">{{ $e->descripcion }}</td>
                    <td>
                        <span class="badge text-bg-{{ ['disponible' => 'success', 'reservado' => 'warning', 'mantenimiento' => 'secondary'][$e->estado] }}">{{ $e->estado }}</span>
                    </td>
                    <td class="text-end">
                        @if($esAdmin)
                            <a href="{{ route('equipos.edit', $e) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form method="POST" action="{{ route('equipos.destroy', $e) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar el equipo?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('equipos.reservar', $e) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Reservar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No hay equipos disponibles por ahora.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection