<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

//filtro de seguridad encargado de controlar el acceso a las rutas
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {   //revisa cada petición antes de dejarla pasar al controlador
        $usuario = $request->user();
        if (! $usuario || ! in_array($usuario->role, $roles, true)) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request); //si el rol es válido, deja continuar la petición normalmente
    }
}
