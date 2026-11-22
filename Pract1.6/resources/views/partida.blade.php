@extends('layouts.app')
@section('titulo', 'Partida #'.$partida->id)

@section('head')
    @if($refrescar)
        <meta http-equiv="refresh" content="2">
    @endif
@endsection

@section('contenido')
@php($casillas = $partida->casillas())

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h1 class="h4">Partida #{{ $partida->id }}</h1>
                <p class="mb-1">
                    <strong>{{ $partida->jugadorX->name }}</strong> (X) vs
                    <strong>{{ $partida->jugadorO?->name ?? '…' }}</strong> (O)
                </p>
                <p class="text-muted small mb-3">Tu símbolo: <strong>{{ $miSimbolo ?? 'espectador' }}</strong></p>

                <div class="d-grid gap-2 mx-auto mb-3" style="grid-template-columns: repeat(3, 1fr); max-width: 300px;">
                    @foreach($casillas as $i => $valor)
                        @if($valor !== '-')
                            <div class="btn btn-outline-dark fs-1 fw-bold disabled" style="aspect-ratio: 1;">{{ $valor }}</div>
                        @elseif($esMiTurno)
                            <form method="POST" action="{{ route('partidas.mover', $partida) }}" class="d-grid">
                                @csrf
                                <input type="hidden" name="posicion" value="{{ $i }}">
                                <button class="btn btn-outline-success fs-1" style="aspect-ratio: 1;">&nbsp;</button>
                            </form>
                        @else
                            <div class="btn btn-outline-secondary disabled" style="aspect-ratio: 1;">&nbsp;</div>
                        @endif
                    @endforeach
                </div>

                <div class="alert alert-{{ $esMiTurno ? 'success' : 'secondary' }} py-2">{{ $mensaje }}</div>

                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('inicio') }}" class="btn btn-outline-secondary">Volver al lobby</a>
                    @if($miSimbolo !== null && $partida->enJuego())
                        <form method="POST" action="{{ route('partidas.abandonar', $partida) }}"
                              onsubmit="return confirm('¿Abandonar la partida?')">
                            @csrf
                            <button class="btn btn-outline-danger">Abandonar</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection