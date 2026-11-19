<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    //admin y empleado: consulta, filtros, ordenamiento y paginación 
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'categoria_id' => ['nullable', 'integer', 'exists:categorias,id'],
            'precio_min' => ['nullable', 'numeric', 'min:0'],
            'precio_max' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['nullable', Rule::in(Producto::ESTADOS)],
            'orden' => ['nullable', Rule::in(array_keys(Producto::ORDENES))],
            'per_page' => ['nullable', Rule::in([10, 15, 25, 50])],
        ]);

        $porPagina = (int) ($filtros['per_page'] ?? 10);

        $productos = Producto::query()
            ->with('categoria')            // evita consultas innecesarias (N+1)
            ->filtrar($filtros)
            ->ordenar($filtros['orden'] ?? null)
            ->paginate($porPagina)
            ->withQueryString();           // conserva filtros, orden y registros por página

        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias', 'porPagina'));
    }

    // ----- Solo administrador -----

    public function create()
    {
        return view('productos.form', [
            'producto' => new Producto(['estado' => 'disponible']),
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return redirect()->route('productos.index')->with('success', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view('productos.form', [
            'producto' => $producto,
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }
}
