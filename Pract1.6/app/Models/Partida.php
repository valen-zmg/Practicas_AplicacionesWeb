<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partida extends Model
{
    public const ESPERANDO = 'esperando';
    public const EN_CURSO = 'en_curso';
    public const TERMINADA = 'terminada';
    public const CANCELADA = 'cancelada';
    //filas
    public const LINEAS = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],
        [0, 3, 6], [1, 4, 7], [2, 5, 8],
        [0, 4, 8], [2, 4, 6],
    ];
    protected $fillable = [
        'jugador_x_id', 'jugador_o_id', 'tablero', 'turno', 'estado', 'resultado', 'ganador_id',
    ];

    public function jugadorX(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugador_x_id')->withTrashed();
    }
    public function jugadorO(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugador_o_id')->withTrashed();
    }
    public function ganador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ganador_id')->withTrashed();
    }
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    //partidas en las que participa un usuario, sea como X o como O
    public function scopeDe(Builder $query, int $userId): Builder
    {
        return $query->where(fn ($q) => $q->where('jugador_x_id', $userId)->orWhere('jugador_o_id', $userId));
    }
    public function participa(int $userId): bool
    {
        return $this->jugador_x_id === $userId || $this->jugador_o_id === $userId;
    }
    public function simboloDe(int $userId): ?string
    {
        return match (true) {
            $this->jugador_x_id === $userId => 'X',
            $this->jugador_o_id === $userId => 'O',
            default => null,
        };
    }
    public function jugadorEnTurnoId(): ?int
    {
        return $this->turno === 'X' ? $this->jugador_x_id : $this->jugador_o_id;
    }

    /** @return string[] las 9 casillas: 'X', 'O' o '-' */
    public function casillas(): array
    {
        return str_split($this->tablero);
    }

    //devuelve 'X', 'O' o null según el tablero recibido
    public static function calcularGanador(array $c): ?string
    {
        foreach (self::LINEAS as [$a, $b, $d]) {
            if ($c[$a] !== '-' && $c[$a] === $c[$b] && $c[$a] === $c[$d]) {
                return $c[$a];
            }
        }

        return null;
    }
    public function enJuego(): bool
    {
        return in_array($this->estado, [self::ESPERANDO, self::EN_CURSO], true);
    }

    //resultado desde el punto de vista de un usuario (para el historial
    public function resultadoPara(int $userId): string
    {
        return match (true) {
            $this->estado === self::CANCELADA => 'Cancelada',
            $this->resultado === 'empate' => 'Empate',
            $this->ganador_id === $userId => $this->resultado === 'abandono' ? 'Ganaste (el rival abandonó)' : 'Ganaste',
            $this->ganador_id !== null => $this->resultado === 'abandono' ? 'Abandonaste' : 'Perdiste',
            default => '—',
        };
    }
}
