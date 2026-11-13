<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoVerificacion extends Model
{
    protected $table = 'codigos_verificacion';

    protected $fillable = ['user_id', 'codigo', 'expires_at', 'intentos'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }
}
