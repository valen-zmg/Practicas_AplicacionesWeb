@extends('layouts.app')
@section('titulo', 'Todas las partidas')
@section('contenido')
<h1 class="h3 mb-3">Todas las partidas</h1>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>#</th><th>X</th><th>O</th><th>Estado</th><th>Creada</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            @forelse($partidas as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->jugadorX->name }}</td>
                    <td>{{ $p->jugadorO?->name ?? '—' }}</td>
                    <td>
                        <span class="badge text-bg-{{ ['esperando' => 'warning', 'en_curso' => 'success', 'terminada' => 'secondary', 'cancelada' => 'dark'][$p->estado] }}">{{ str_replace('_', ' ', $p->estado) }}</span>
                    </td>
                    <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-end">
                        <a href="{{ route('partidas.show', $p) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @if($p->enJuego())
                            <form method="POST" action="{{ route('admin.partidas.cancelar', $p) }}" class="d-inline"
                                  onsubmit="return confirm('¿Cancelar esta partida?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Todavía no hay partidas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $partidas->links() }}</div>
@endsection