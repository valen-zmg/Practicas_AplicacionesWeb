<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('inicio');
        }

        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Un usuario eliminado lógicamente no se encuentra aquí (SoftDeletes), así que no puede entrar
        $usuario = User::where('email', $datos['email'])->first();

        // Mismo mensaje para cualquier fallo: no revelamos si el correo existe
        if (! $usuario
            || ! Hash::check($datos['password'], $usuario->password)
            || ! in_array($usuario->role, [User::ADMIN, User::JUGADOR], true)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
        }

        Auth::login($usuario);
        $request->session()->regenerate(); // nuevo ID de sesión al autenticarse

        return redirect()->route('inicio');
    }

    public function showRegister()
    {
        return view('register');
    }

    /** Registro público: siempre crea un usuario con rol "jugador", sin importar lo que envíe el formulario. */
    public function register(Request $request): RedirectResponse
    {
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

        $usuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'role' => User::JUGADOR,
        ]);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('inicio');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();      // cierra SOLO la sesión de este navegador
        $request->session()->regenerateToken(); // nuevo token CSRF

        return redirect()->route('login');
    }
}
