@extends('layouts.app')
@section('titulo', 'Lobby')
@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Lobby</h1>
    <form method="POST" action="{{ route('partidas.crear') }}">
        @csrf
        <button class="btn btn-primary">Crear nueva partida</button>
    </form>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header">Mis partidas en juego</div>
            <ul class="list-group list-group-flush">
                @forelse($misPartidas as $p)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            #{{ $p->id }} · {{ $p->jugadorX->name }} vs {{ $p->jugadorO?->name ?? '…' }}
                            <span class="badge text-bg-{{ $p->estado === 'en_curso' ? 'success' : 'warning' }}">{{ str_replace('_', ' ', $p->estado) }}</span>
                        </span>
                        <a href="{{ route('partidas.show', $p) }}" class="btn btn-sm btn-outline-primary">Abrir</a>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No tienes partidas activas.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header">Partidas disponibles para unirse</div>
            <ul class="list-group list-group-flush">
                @forelse($disponibles as $p)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>#{{ $p->id }} · creada por {{ $p->jugadorX->name }}</span>
                        <form method="POST" action="{{ route('partidas.unirse', $p) }}">
                            @csrf
                            <button class="btn btn-sm btn-success">Unirme</button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nadie ha creado una partida todavía.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">Jugadores</div>
            <ul class="list-group list-group-flush">
                @forelse($jugadores as $j)
                    <li class="list-group-item d-flex justify-content-between">
                        {{ $j->name }}
                        @if(in_array($j->id, $ocupados))
                            <span class="badge text-bg-warning">En partida</span>
                        @else
                            <span class="badge text-bg-success">Disponible</span>
                        @endif
                    </li>
                @empty
                    <li class="list-group-item text-muted">No hay otros jugadores registrados.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection