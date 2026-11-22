@extends('layouts.app')
@section('titulo', 'Mi historial')
@section('contenido')
<h1 class="h3 mb-3">Mi historial de partidas</h1>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>#</th><th>X</th><th>O</th><th>Resultado</th><th>Fecha</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($partidas as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->jugadorX->name }}</td>
                    <td>{{ $p->jugadorO?->name ?? '—' }}</td>
                    <td>{{ $p->resultadoPara(auth()->id()) }}</td>
                    <td>{{ $p->updated_at->format('d/m/Y H:i') }}</td>
                    <td class="text-end">
                        <a href="{{ route('partidas.show', $p) }}" class="btn btn-sm btn-outline-secondary">Ver tablero</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Aún no has terminado ninguna partida.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $partidas->links() }}</div>
@endsection