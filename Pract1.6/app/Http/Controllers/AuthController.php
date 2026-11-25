<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller //encargado de manejar el inicio de sesión, registro y cierre de sesión
{
    public function showLogin()
    {   //muestra la pantalla de inicio de sesion
        if (Auth::check()) {
            return redirect()->route('inicio');
        } //si el usuario ya había iniciado sesión, lo manda directo a la pantalla de inicio
        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {   // 1. verifica que ambos campos lleguen con el formato correcto
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. busca al usuario activo por su correo.
        $usuario = User::where('email', $datos['email'])->first();

        // 3. muestra el mismo mensaje
        if (! $usuario
            || ! Hash::check($datos['password'], $usuario->password)
            || ! in_array($usuario->role, [User::ADMIN, User::JUGADOR], true)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
        }
        // 4. inicia sesión, crea un nuevo identificador de sesión por seguridad y lo lleva al inicio
        Auth::login($usuario);
        $request->session()->regenerate(); // nuevo ID de sesión al autenticarse
        return redirect()->route('inicio');
    }

    public function showRegister()
    {   //formulario de registro para crear una cuenta nueva
        return view('register');
    }

    //registro público: siempre crea un usuario con rol "jugador", sin importar lo que envíe el formulario
    public function register(Request $request): RedirectResponse
    {   //validacion y requerimientos
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Ya existe una cuenta con ese correo.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);
        //guarda el usuario asignándole directamente el rol de Jugador
        $usuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'role' => User::JUGADOR,
        ]);
        //inicia sesión automáticamente con la cuenta recién creada
        Auth::login($usuario);
        $request->session()->regenerate();
        return redirect()->route('inicio');
    }

    public function logout(Request $request): RedirectResponse
    {   //cierra la sesión del navegador actual
        Auth::logout();
        $request->session()->invalidate();      // cierra SOLO la sesión de este navegador
        $request->session()->regenerateToken(); // nuevo token CSRF
        return redirect()->route('login');
    }
}
