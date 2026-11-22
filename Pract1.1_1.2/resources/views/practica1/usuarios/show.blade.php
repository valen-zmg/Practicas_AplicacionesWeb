@extends('layouts.app')
@section('titulo', 'Detalle de usuario')
@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">{{ $usuario->name }}</h1>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Correo</dt><dd class="col-sm-8">{{ $usuario->email }}</dd>
                    <dt class="col-sm-4">Rol</dt><dd class="col-sm-8">{{ $usuario->role }}</dd>
                    <dt class="col-sm-4">Registrado</dt><dd class="col-sm-8">{{ $usuario->created_at->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-4">Actualizado</dt><dd class="col-sm-8">{{ $usuario->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
                <a href="{{ route('p1.usuarios.index') }}" class="btn btn-outline-secondary mt-3">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection