<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alumno extends Model
{
    public const ORDENES = ['nombre', 'calificacion', 'faltas'];
    public const MAX_FALTAS = 60;
    public const MAX_CALIFICACION = 10;

    protected $fillable = ['user_id', 'nombre', 'faltas', 'calificacion'];

    protected function casts(): array
    {
        return ['faltas' => 'integer', 'calificacion' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Equivalencia alfabética calculada automáticamente (nunca se captura).
     * NA < 7.0 | SA 7.0 - 8.4 | DE 8.5 - 9.4 | AU 9.5 - 10
     */
    public function getEquivalenciaAttribute(): string
    {
        $c = (float) $this->calificacion;

        return match (true) {
            $c < 7.0 => 'NA',
            $c < 8.5 => 'SA',
            $c < 9.5 => 'DE',
            default => 'AU',
        };
    }

    public function getEquivalenciaNombreAttribute(): string
    {
        return ['NA' => 'No Acreditado', 'SA' => 'Suficiente', 'DE' => 'Destacado', 'AU' => 'Autónomo'][$this->equivalencia];
    }

    public function getColorEquivalenciaAttribute(): string
    {
        return ['NA' => 'danger', 'SA' => 'warning', 'DE' => 'info', 'AU' => 'success'][$this->equivalencia];
    }
}
