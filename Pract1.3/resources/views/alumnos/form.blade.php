@extends('layouts.app')
@section('titulo', $alumno->exists ? 'Editar alumno' : 'Registrar alumno')
@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">{{ $alumno->exists ? 'Editar alumno' : 'Registrar alumno' }}</h1>

                <form method="POST" action="{{ $alumno->exists ? route('alumnos.update', $alumno) : route('alumnos.store') }}">
                    @csrf
                    @if($alumno->exists)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del alumno</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $alumno->nombre) }}"
                               class="form-control @error('nombre') is-invalid @enderror">
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo (para iniciar sesión)</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $alumno->user?->email) }}"
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            {{ $alumno->exists ? 'Contraseña (vacía = sin cambios)' : 'Contraseña' }}
                        </label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <label for="faltas" class="form-label">Faltas (0-60)</label>
                            <input type="number" id="faltas" name="faltas" min="0" max="60" step="1"
                                   value="{{ old('faltas', $alumno->faltas ?? 0) }}"
                                   class="form-control @error('faltas') is-invalid @enderror">
                            @error('faltas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col mb-3">
                            <label for="calificacion" class="form-label">Calificación (0-10)</label>
                            <input type="number" id="calificacion" name="calificacion" min="0" max="10" step="0.01"
                                   value="{{ old('calificacion', $alumno->calificacion ?? 0) }}"
                                   class="form-control @error('calificacion') is-invalid @enderror">
                            @error('calificacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <p class="small text-muted">La equivalencia (NA, SA, DE, AU) se calcula sola al guardar.</p>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Guardar</button>
                        <a href="{{ route('alumnos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection