@extends('layouts.app')
@section('titulo', $producto->exists ? 'Editar producto' : 'Nuevo producto')
@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">{{ $producto->exists ? 'Editar producto' : 'Nuevo producto' }}</h1>

                <form method="POST" action="{{ $producto->exists ? route('productos.update', $producto) : route('productos.store') }}">
                    @csrf
                    @if($producto->exists)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}"
                               class="form-control @error('nombre') is-invalid @enderror">
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                                  class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $producto->descripcion) }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Categoría</label>
                        <select id="categoria_id" name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror">
                            <option value="">Selecciona una categoría…</option>
                            @foreach($categorias as $c)
                                <option value="{{ $c->id }}" @selected(old('categoria_id', $producto->categoria_id) == $c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                        @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" id="precio" name="precio" step="0.01"
                                   value="{{ old('precio', $producto->precio) }}"
                                   class="form-control @error('precio') is-invalid @enderror">
                            @error('precio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col mb-3">
                            <label for="existencias" class="form-label">Existencias</label>
                            <input type="number" id="existencias" name="existencias" step="1"
                                   value="{{ old('existencias', $producto->existencias ?? 0) }}"
                                   class="form-control @error('existencias') is-invalid @enderror">
                            @error('existencias')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror">
                            @foreach(\App\Models\Producto::ESTADOS as $e)
                                <option value="{{ $e }}" @selected(old('estado', $producto->estado) === $e)>{{ ucfirst($e) }}</option>
                            @endforeach
                        </select>
                        @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Guardar</button>
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection