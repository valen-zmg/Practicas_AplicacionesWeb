@extends('layouts.app')
@section('titulo', 'Verificación')
@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4">Ingresa tu código</h1>
                <p class="text-muted">Enviamos un código a tu correo registrado. Vence en 5 minutos.</p>
                <form method="POST" action="{{ route('p1.verificar.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código de verificación</label>
                        <input type="text" id="codigo" name="codigo" maxlength="6" inputmode="numeric"
                               autocomplete="one-time-code" autofocus required
                               class="form-control @error('codigo') is-invalid @enderror">
                        @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-primary w-100">Verificar y entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection