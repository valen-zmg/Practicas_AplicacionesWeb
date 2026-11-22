<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservacion extends Model
{
    public const ACTIVA = 'activa';
    public const CANCELADA = 'cancelada';

    protected $table = 'reservaciones';

    protected $fillable = ['user_id', 'equipo_id', 'estado', 'cancelada_at'];

    protected function casts(): array
    {
        return ['cancelada_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }
}