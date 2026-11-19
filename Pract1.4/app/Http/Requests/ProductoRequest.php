<?php

namespace App\Http\Requests;

use App\Models\Producto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Doble protección: además del middleware role:admin en las rutas
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('producto')?->id;

        return [
            'nombre' => ['required', 'string', 'max:150', Rule::unique('productos', 'nombre')->ignore($id)],
            'descripcion' => ['required', 'string', 'max:1000'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'precio' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'existencias' => ['required', 'integer', 'min:0'],
            'estado' => ['required', Rule::in(Producto::ESTADOS)],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con ese nombre.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'categoria_id.required' => 'Selecciona una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no existe en el catálogo.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser numérico.',
            'precio.gt' => 'El precio debe ser mayor que cero.',
            'existencias.required' => 'Las existencias son obligatorias.',
            'existencias.integer' => 'Las existencias deben ser un número entero.',
            'existencias.min' => 'Las existencias deben ser iguales o mayores que cero.',
            'estado.required' => 'Selecciona un estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
