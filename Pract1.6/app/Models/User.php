<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable //representa a los usuarios del sistema de partidas del gatiko
{
    use Notifiable, SoftDeletes;
    //roles permitidos
    public const ADMIN = 'admin';
    public const JUGADOR = 'jugador';
    //campos que se pueden guardar y actualizar
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',//lo convierte en un objeto de fecha
            'password' => 'hashed', //encripta automáticamente cualquier contraseña
        ];
    }

    public function esAdmin(): bool //comprueba si este usuario tiene el rol de administrador
    {
        return $this->role === self::ADMIN; //redirigirlo a su panel de gestión
    }
}
