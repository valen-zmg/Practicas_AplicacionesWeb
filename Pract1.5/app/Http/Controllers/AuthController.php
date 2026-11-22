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
            return redirect()->route('equipos.index');
        }

        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $usuario = User::where('email', $datos['email'])->first();

        if (! $usuario
            || ! Hash::check($datos['password'], $usuario->password)
            || ! in_array($usuario->role, [User::ADMIN, User::ESTUDIANTE], true)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
        }

        Auth::login($usuario);
        $request->session()->regenerate(); //nuevo ID de sesión al autenticarse

        return redirect()->route('equipos.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();      
        $request->session()->regenerateToken(); //nuevo token 

        return redirect()->route('login');
    }
}
