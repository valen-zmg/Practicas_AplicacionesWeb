<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller //para admins que permite gestionar las cuentas
{
    public function index()
    {   //listado de usuarios
        $usuarios = User::orderBy('name')->paginate(15);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {   //dar de alta nuevo usuario
        return view('admin.usuarios.form', ['usuario' => new User()]);
    }

    public function store(Request $request): RedirectResponse
    {   //valida y guarda nuevo usuario
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in([User::ADMIN, User::JUGADOR])],
        ], $this->mensajes());

        User::create($datos);
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado.');
    }

    public function edit(User $usuario)
    {   //formulario de edicion
        return view('admin.usuarios.form', compact('usuario'));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {   //actualiza los datos
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in([User::ADMIN, User::JUGADOR])],
        ], $this->mensajes());
        //evita borrar o sobreescribir la contra
        if (empty($datos['password'])) {
            unset($datos['password']); //vacía = sin cambios
        }

        $usuario->update($datos);
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {   //da de baja a un usuario mediante eliminación lógica
        abort_if($usuario->id === $request->user()->id, 403, 'No puedes eliminar tu propia cuenta.');
        $usuario->delete(); //sus partidas se conservan en el historial
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado.');
    }

    private function mensajes(): array
    {   //mensajes de error
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role.in' => 'El rol seleccionado no es válido.',
        ];
    }
}
