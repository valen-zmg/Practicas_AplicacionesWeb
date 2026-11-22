@extends('layouts.app')
@section('titulo', 'Usuarios')
@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Usuarios</h1>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">Nuevo usuario</a>
</div>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            @foreach($usuarios as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge text-bg-{{ $u->esAdmin() ? 'danger' : 'primary' }}">{{ $u->role }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.usuarios.edit', $u) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.usuarios.destroy', $u) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar a {{ $u->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $usuarios->links() }}</div>
@endsection