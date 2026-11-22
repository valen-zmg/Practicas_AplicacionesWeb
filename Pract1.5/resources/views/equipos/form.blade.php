@extends('layouts.app')
@section('titulo', $equipo->exists ? 'Editar equipo' : 'Nuevo equipo')
@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">{{ $equipo->exists ? 'Editar equipo' : 'Nuevo equipo' }}</h1>

                <form method="POST" action="{{ $equipo->exists ? route('equipos.update', $equipo) : route('equipos.store') }}">
                    @csrf
                    @if($equipo->exists)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código (p. ej. PC-09)</label>
                        <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $equipo->codigo) }}"
                               class="form-control @error('codigo') is-invalid @enderror">
                        @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $equipo->nombre) }}"
                               class="form-control @error('nombre') is-invalid @enderror">
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                                  class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $equipo->descripcion) }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        @if($equipo->estado === 'reservado')
                            <input type="text" class="form-control" value="reservado (lo controla el sistema)" disabled>
                        @else
                            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror">
                                @foreach(['disponible', 'mantenimiento'] as $e)
                                    <option value="{{ $e }}" @selected(old('estado', $equipo->estado) === $e)>{{ $e }}</option>
                                @endforeach
                            </select>
                            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Guardar</button>
                        <a href="{{ route('equipos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection