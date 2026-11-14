<?php

namespace App\Http\Controllers\Practica1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practica1\UsuarioRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        // La papelera (eliminados lógicamente) solo existe para el admin
        $papelera = $request->boolean('papelera') && $request->user()->esAdmin();

        $consulta = User::query()->whereIn('role', User::ROLES_P1);
        if ($papelera) {
            $consulta->onlyTrashed();
        }
        $usuarios = $consulta->orderBy('name')->paginate(10)->withQueryString();
        return view('practica1.usuarios.index', compact('usuarios', 'papelera'));
    }

    public function create()
    {
        return view('practica1.usuarios.form', ['usuario' => new User()]);
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        // Solo el admin elige el rol; el empleado siempre registra empleados
        $datos['role'] = $request->user()->esAdmin() ? ($datos['role'] ?? User::EMPLEADO) : User::EMPLEADO;
        User::create($datos);
        return redirect()->route('p1.usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function show(User $usuario)
    {
        $this->asegurarModulo($usuario);
        return view('practica1.usuarios.show', compact('usuario'));
    }

    public function edit(Request $request, User $usuario)
    {
        $this->autorizarEdicion($request->user(), $usuario);
        return view('practica1.usuarios.form', compact('usuario'));
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $this->autorizarEdicion($request->user(), $usuario);

        $datos = $request->validated();
        if (empty($datos['password'])) {
            unset($datos['password']);
        }
        if (! $request->user()->esAdmin()) {
            unset($datos['role']); //el empleado no puede cambiar roles
        }
        $usuario->update($datos);
        return redirect()->route('p1.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    //Solo admin. tipo = logica (Soft Delete) o permanente (forceDelete) 
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $request->validate(['tipo' => ['required', 'in:logica,permanente']]);
        $usuario = User::withTrashed()->whereIn('role', User::ROLES_P1)->findOrFail($id);
        abort_if($usuario->id === $request->user()->id, 403, 'No puedes eliminar tu propia cuenta.');
        if ($request->input('tipo') === 'logica') {
            $usuario->delete();
            $mensaje = 'Usuario deshabilitado (eliminación lógica). Puedes restaurarlo desde la papelera.';
        } else {
            $usuario->forceDelete();
            $mensaje = 'Usuario eliminado permanentemente de la base de datos.';
        }
        return redirect()->route('p1.usuarios.index')->with('success', $mensaje);
    }

    //Solo admin. Recupera un usuario eliminado lógicamente
    public function restore(int $id): RedirectResponse
    {
        $usuario = User::onlyTrashed()->whereIn('role', User::ROLES_P1)->findOrFail($id);
        $usuario->restore();
        return redirect()->route('p1.usuarios.index', ['papelera' => 1])->with('success', 'Usuario restaurado.');
    }

    private function asegurarModulo(User $usuario): void
    {
        abort_unless(in_array($usuario->role, User::ROLES_P1, true), 404);
    }

    private function autorizarEdicion(User $actual, User $objetivo): void
    {
        $this->asegurarModulo($objetivo);
        abort_if($objetivo->esAdmin() && ! $actual->esAdmin(), 403, 'Un empleado no puede modificar a un administrador.');
    }
}
