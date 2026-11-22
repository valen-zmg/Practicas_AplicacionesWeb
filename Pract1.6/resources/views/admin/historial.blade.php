@extends('layouts.app')
@section('titulo', 'Historial general')
@section('contenido')
<h1 class="h3 mb-3">Historial general de partidas</h1>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>#</th><th>X</th><th>O</th><th>Resultado</th><th>Ganador</th><th>Fecha</th></tr>
            </thead>
            <tbody>
            @forelse($partidas as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->jugadorX->name }}</td>
                    <td>{{ $p->jugadorO?->name ?? '—' }}</td>
                    <td>{{ $p->resultado ?? $p->estado }}</td>
                    <td>{{ $p->ganador?->name ?? '—' }}</td>
                    <td>{{ $p->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Todavía no hay partidas terminadas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $partidas->links() }}</div>
@endsection