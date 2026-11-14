@extends('layouts.app')
@section('titulo', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')
@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">{{ $usuario->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>
                <form method="POST" action="{{ $usuario->exists ? route('p1.usuarios.update', $usuario) : route('p1.usuarios.store') }}">
                    @csrf
                    @if($usuario->exists) @method('PUT') @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}"
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}"
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            {{ $usuario->exists ? 'Contraseña (déjala vacía para no cambiarla)' : 'Contraseña' }}
                        </label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Solo el admin ve y puede cambiar el rol --}}
                    @if(auth()->user()->esAdmin())
                        <div class="mb-3">
                            <label for="role" class="form-label">Rol</label>
                            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
                                @foreach(['empleado', 'admin'] as $rol)
                                    <option value="{{ $rol }}" @selected(old('role', $usuario->role) === $rol)>{{ $rol }}</option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Guardar</button>
                        <a href="{{ route('p1.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection