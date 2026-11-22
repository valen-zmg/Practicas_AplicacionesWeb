@extends('layouts.app')
@section('titulo', 'Todas las reservaciones')
@section('contenido')
<h1 class="h3 mb-3">Todas las reservaciones</h1>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>#</th><th>Usuario</th><th>Equipo</th><th>Fecha</th><th>Estado</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            @forelse($reservaciones as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->user->name }}</td>
                    <td>{{ $r->equipo->codigo }}</td>
                    <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="badge text-bg-{{ $r->estado === 'activa' ? 'success' : 'secondary' }}">{{ $r->estado }}</span></td>
                    <td class="text-end">
                        @if($r->estado === 'activa')
                            <form method="POST" action="{{ route('reservaciones.cancelar', $r) }}" class="d-inline"
                                  onsubmit="return confirm('¿Cancelar esta reservación?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay reservaciones.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $reservaciones->links() }}</div>
@endsection