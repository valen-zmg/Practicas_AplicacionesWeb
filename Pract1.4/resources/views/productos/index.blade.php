@extends('layouts.app')
@section('titulo', 'Productos')
@section('contenido')
@php($esAdmin = auth()->user()->esAdmin())

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Catálogo de productos</h1>
    @if($esAdmin)
        <a href="{{ route('productos.create') }}" class="btn btn-primary">Nuevo producto</a>
    @endif
</div>

<form method="GET" action="{{ route('productos.index') }}" class="card card-body shadow-sm mb-3">
    <div class="row g-2">
        <div class="col-md-3">
            <label class="form-label small mb-0" for="buscar">Buscar por nombre</label>
            <input type="text" id="buscar" name="buscar" value="{{ request('buscar') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-0" for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="form-select form-select-sm">
                <option value="">Todas</option>
                @foreach($categorias as $c)
                    <option value="{{ $c->id }}" @selected(request('categoria_id') == $c->id)>{{ $c->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-0" for="precio_min">Precio mín.</label>
            <input type="number" step="0.01" min="0" id="precio_min" name="precio_min" value="{{ request('precio_min') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-0" for="precio_max">Precio máx.</label>
            <input type="number" step="0.01" min="0" id="precio_max" name="precio_max" value="{{ request('precio_max') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-0" for="estado">Estado</label>
            <select id="estado" name="estado" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="disponible" @selected(request('estado') === 'disponible')>Disponible</option>
                <option value="agotado" @selected(request('estado') === 'agotado')>Agotado</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small mb-0" for="orden">Ordenar por</label>
            <select id="orden" name="orden" class="form-select form-select-sm">
                @foreach(\App\Models\Producto::ETIQUETAS_ORDEN as $clave => $texto)
                    <option value="{{ $clave }}" @selected(request('orden', 'nombre_asc') === $clave)>{{ $texto }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-0" for="per_page">Por página</label>
            <select id="per_page" name="per_page" class="form-select form-select-sm">
                @foreach([10, 15, 25, 50] as $n)
                    <option value="{{ $n }}" @selected($porPagina === $n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 d-flex align-items-end gap-2">
            <button class="btn btn-sm btn-primary">Aplicar filtros</button>
            <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar filtros</a>
        </div>
    </div>
    @if($errors->any())
        <div class="text-danger small mt-2">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Descripción</th><th>Categoría</th>
                <th class="text-end">Precio</th><th class="text-end">Existencias</th><th>Estado</th><th>Registro</th>
                @if($esAdmin)<th class="text-end">Acciones</th>@endif
            </tr>
            </thead>
            <tbody>
            @forelse($productos as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td class="small text-muted">{{ \Illuminate\Support\Str::limit($p->descripcion, 50) }}</td>
                    <td>{{ $p->categoria->nombre }}</td>
                    <td class="text-end">${{ number_format($p->precio, 2) }}</td>
                    <td class="text-end">{{ $p->existencias }}</td>
                    <td>
                        <span class="badge text-bg-{{ $p->estado === 'disponible' ? 'success' : 'secondary' }}">{{ ucfirst($p->estado) }}</span>
                    </td>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    @if($esAdmin)
                        <td class="text-end">
                            <a href="{{ route('productos.edit', $p) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form method="POST" action="{{ route('productos.destroy', $p) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No se encontraron productos con esos filtros.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <span class="small text-muted">
        Total encontrados: <strong>{{ $productos->total() }}</strong> ·
        Mostrando {{ $productos->firstItem() ?? 0 }}–{{ $productos->lastItem() ?? 0 }} ·
        Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}
    </span>
    {{ $productos->links() }}
</div>
@endsection