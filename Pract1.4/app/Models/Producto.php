<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    public const ESTADOS = ['disponible', 'agotado'];
    public const ORDENES = [
        'nombre_asc' => ['nombre', 'asc'],
        'nombre_desc' => ['nombre', 'desc'],
        'precio_desc' => ['precio', 'desc'],
        'precio_asc' => ['precio', 'asc'],
        'existencias_desc' => ['existencias', 'desc'],
        'existencias_asc' => ['existencias', 'asc'],
        'fecha_desc' => ['created_at', 'desc'],
        'fecha_asc' => ['created_at', 'asc'],
    ];

    public const ETIQUETAS_ORDEN = [
        'nombre_asc' => 'Nombre (A-Z)',
        'nombre_desc' => 'Nombre (Z-A)',
        'precio_desc' => 'Precio (mayor a menor)',
        'precio_asc' => 'Precio (menor a mayor)',
        'existencias_desc' => 'Existencias (mayor a menor)',
        'existencias_asc' => 'Existencias (menor a mayor)',
        'fecha_desc' => 'Fecha de registro (más recientes)',
        'fecha_asc' => 'Fecha de registro (más antiguos)',
    ];

    protected $fillable = ['nombre', 'descripcion', 'categoria_id', 'precio', 'existencias', 'estado'];

    protected function casts(): array
    {
        return ['precio' => 'decimal:2', 'existencias' => 'integer'];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    //Filtros combinables: solo se aplican los que llegan con valor
    public function scopeFiltrar(Builder $query, array $f): Builder
    {
        return $query
            ->when($f['buscar'] ?? null, fn ($q, $v) => $q->where('nombre', 'like', '%'.$v.'%'))
            ->when($f['categoria_id'] ?? null, fn ($q, $v) => $q->where('categoria_id', $v))
            ->when(isset($f['precio_min']) && $f['precio_min'] !== '', fn ($q) => $q->where('precio', '>=', $f['precio_min']))
            ->when(isset($f['precio_max']) && $f['precio_max'] !== '', fn ($q) => $q->where('precio', '<=', $f['precio_max']))
            ->when($f['estado'] ?? null, fn ($q, $v) => $q->where('estado', $v));
    }

    public function scopeOrdenar(Builder $query, ?string $orden): Builder
    {
        [$columna, $direccion] = self::ORDENES[$orden] ?? self::ORDENES['nombre_asc'];

        return $query->orderBy($columna, $direccion)->orderBy('id');
    }
}
