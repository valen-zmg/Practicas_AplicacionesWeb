<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ALUMNO = 'alumno';
    public const PROFESOR = 'profesor';

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function esProfesor(): bool
    {
        return $this->role === self::PROFESOR;
    }

    public function esAlumno(): bool
    {
        return $this->role === self::ALUMNO;
    }

    public function alumno(): HasOne
    {
        return $this->hasOne(Alumno::class);
    }
}
