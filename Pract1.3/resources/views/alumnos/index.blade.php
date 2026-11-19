@extends('layouts.app')
@section('titulo', 'Alumnos')
@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Alumnos</h1>
    <a href="{{ route('alumnos.create') }}" class="btn btn-primary">Registrar alumno</a>
</div>

<p class="text-muted small">Pulsa el encabezado de una columna para ordenar (A-Z / Z-A, mayor a menor o viceversa).</p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th>
                    <a href="{{ route('alumnos.index', ['orden' => 'nombre', 'dir' => (request('orden', 'nombre') == 'nombre' && request('dir', 'asc') == 'asc') ? 'desc' : 'asc']) }}"
                       class="text-decoration-none text-reset">Nombre del alumno</a>
                    @if(request('orden', 'nombre') == 'nombre') {{ request('dir', 'asc') == 'asc' ? '▲' : '▼' }} @endif
                </th>
                <th>
                    <a href="{{ route('alumnos.index', ['orden' => 'faltas', 'dir' => (request('orden') == 'faltas' && request('dir') == 'asc') ? 'desc' : 'asc']) }}"
                       class="text-decoration-none text-reset">Faltas</a>
                    @if(request('orden') == 'faltas') {{ request('dir') == 'asc' ? '▲' : '▼' }} @endif
                </th>
                <th>
                    <a href="{{ route('alumnos.index', ['orden' => 'calificacion', 'dir' => (request('orden') == 'calificacion' && request('dir') == 'asc') ? 'desc' : 'asc']) }}"
                       class="text-decoration-none text-reset">Calificación</a>
                    @if(request('orden') == 'calificacion') {{ request('dir') == 'asc' ? '▲' : '▼' }} @endif
                </th>
                <th>Equivalencia</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse($alumnos as $a)
                <tr>
                    <td>{{ $a->nombre }}</td>
                    <td>{{ $a->faltas }}</td>
                    <td>{{ number_format($a->calificacion, 2) }}</td>
                    <td>
                        <span class="badge text-bg-{{ $a->color_equivalencia }}">{{ $a->equivalencia }}</span>
                        <span class="small text-muted">{{ $a->equivalencia_nombre }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('alumnos.edit', $a) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form method="POST" action="{{ route('alumnos.destroy', $a) }}" class="d-inline"
                              onsubmit="return confirm('¿Eliminar a {{ $a->nombre }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Aún no hay alumnos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center">
    <span class="small text-muted">
        Total: {{ $alumnos->total() }} · Página {{ $alumnos->currentPage() }} de {{ $alumnos->lastPage() }}
    </span>
    {{ $alumnos->links() }}
</div>
@endsection