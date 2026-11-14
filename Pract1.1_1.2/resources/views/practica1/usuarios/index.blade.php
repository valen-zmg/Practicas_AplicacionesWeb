@extends('layouts.app')
@section('titulo', 'Usuarios')
@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Usuarios</h1>
    <a href="{{ route('p1.usuarios.create') }}" class="btn btn-primary">Nuevo usuario</a>
</div>

@if(auth()->user()->esAdmin())
    <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link {{ $papelera ? '' : 'active' }}" href="{{ route('p1.usuarios.index') }}">Activos</a></li>
        <li class="nav-item"><a class="nav-link {{ $papelera ? 'active' : '' }}" href="{{ route('p1.usuarios.index', ['papelera' => 1]) }}">Papelera (eliminación lógica)</a></li>
    </ul>
@endif

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>{{ $papelera ? 'Eliminado' : 'Registro' }}</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            @forelse($usuarios as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge text-bg-{{ $u->esAdmin() ? 'danger' : 'primary' }}">{{ $u->role }}</span></td>
                    <td>{{ ($papelera ? $u->deleted_at : $u->created_at)?->format('d/m/Y H:i') }}</td>
                    <td class="text-end">
                        @if(! $papelera)
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('p1.usuarios.show', $u) }}">Ver</a>
                            @if(! ($u->esAdmin() && ! auth()->user()->esAdmin()))
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('p1.usuarios.edit', $u) }}">Editar</a>
                            @endif
                        @endif

                        {{-- Estas opciones SOLO se dibujan para el admin (el backend además las protege) --}}
                        @if(auth()->user()->esAdmin() && $u->id !== auth()->id())
                            @if($papelera)
                                <form method="POST" action="{{ route('p1.usuarios.restore', $u->id) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Restaurar</button>
                                </form>
                                <form method="POST" action="{{ route('p1.usuarios.destroy', $u->id) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar definitivamente? No se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="tipo" value="permanente">
                                    <button class="btn btn-sm btn-danger">Eliminar permanente</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('p1.usuarios.destroy', $u->id) }}" class="d-inline-flex gap-1"
                                      onsubmit="return confirm('¿Confirmas la eliminación?')">
                                    @csrf @method('DELETE')
                                    <select name="tipo" class="form-select form-select-sm w-auto">
                                        <option value="logica">Lógica (Soft Delete)</option>
                                        <option value="permanente">Permanente (Delete)</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No hay usuarios en esta vista.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center">
    <span class="small text-muted">Total: {{ $usuarios->total() }}</span>
    {{ $usuarios->links() }}
</div>
@endsection