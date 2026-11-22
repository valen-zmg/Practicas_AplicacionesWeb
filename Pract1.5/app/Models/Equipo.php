<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    public const DISPONIBLE = 'disponible';
    public const RESERVADO = 'reservado';
    public const MANTENIMIENTO = 'mantenimiento';

    protected $fillable = ['codigo', 'nombre', 'descripcion', 'estado'];

    public function reservaciones(): HasMany
    {
        return $this->hasMany(Reservacion::class);
    }
}
