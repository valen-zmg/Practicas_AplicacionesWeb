@extends('layouts.app')
@section('titulo', 'Mis reservaciones')
@section('contenido')
<h1 class="h3 mb-3">Mis reservaciones</h1>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>#</th><th>Equipo</th><th>Reservado</th><th>Estado</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            @forelse($reservaciones as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->equipo->codigo }} · {{ $r->equipo->nombre }}</td>
                    <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="badge text-bg-{{ $r->estado === 'activa' ? 'success' : 'secondary' }}">{{ $r->estado }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('reservaciones.show', $r) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @if($r->estado === 'activa')
                            <form method="POST" action="{{ route('reservaciones.cancelar', $r) }}" class="d-inline"
                                  onsubmit="return confirm('¿Cancelar la reservación?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Todavía no has reservado ningún equipo.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection