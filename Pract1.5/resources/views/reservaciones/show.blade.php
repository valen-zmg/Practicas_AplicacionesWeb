@extends('layouts.app')
@section('titulo', 'Detalle de reservación')
@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Reservación #{{ $reservacion->id }}</h1>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Usuario</dt><dd class="col-sm-8">{{ $reservacion->user->name }}</dd>
                    <dt class="col-sm-4">Equipo</dt><dd class="col-sm-8">{{ $reservacion->equipo->codigo }} · {{ $reservacion->equipo->nombre }}</dd>
                    <dt class="col-sm-4">Estado</dt><dd class="col-sm-8">{{ $reservacion->estado }}</dd>
                    <dt class="col-sm-4">Fecha</dt><dd class="col-sm-8">{{ $reservacion->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
                <a href="{{ route('equipos.index') }}" class="btn btn-outline-secondary mt-3">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection