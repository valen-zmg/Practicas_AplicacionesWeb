<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model //registra cada jugada individual realizada dentro de una partida
{   //nombre y campos que se pueden guardar y actualizar
    protected $table = 'movimientos';
    protected $fillable = ['partida_id', 'user_id', 'posicion', 'simbolo'];

    public function partida(): BelongsTo
    {   //relacion movimiento pertenece a partida
        return $this->belongsTo(Partida::class);
    }

    public function user(): BelongsTo
    {   //relacion movimiento pertenece a usuario
        return $this->belongsTo(User::class);
    }
}
