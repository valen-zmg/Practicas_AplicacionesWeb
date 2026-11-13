<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    public const ADMIN = 'admin';
    public const EMPLEADO = 'empleado';

    /** Roles que maneja el CRUD de esta práctica */
    public const ROLES_P1 = [self::ADMIN, self::EMPLEADO];

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function esAdmin(): bool
    {
        return $this->role === self::ADMIN;
    }

    public function tieneRol(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
