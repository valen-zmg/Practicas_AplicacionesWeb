<?php

namespace App\Http\Controllers\Practica1;

use App\Http\Controllers\Controller;
use App\Mail\CodigoVerificacionMail;
use App\Models\CodigoVerificacion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    private const VIGENCIA_MINUTOS = 5;
    private const MAX_INTENTOS = 5;

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('p1.inicio');
        }

        return view('practica1.login');
    }

    /** ETAPA 1: correo y contraseña */
    public function login(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $usuario = User::where('email', $datos['email'])->first();

        // Mismo mensaje para cualquier fallo: no revelamos si el correo existe
        if (! $usuario
            || ! Hash::check($datos['password'], $usuario->password)
            || ! $usuario->tieneRol(...User::ROLES_P1)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
        }

        // Empleado: acceso directo
        if (! $usuario->esAdmin()) {
            Auth::login($usuario);
            $request->session()->regenerate();

            return redirect()->route('p1.inicio');
        }

        // ETAPA 2: se genera y se envía el código. Todavía NO existe sesión autenticada.
        $codigo = (string) random_int(100000, 999999);

        CodigoVerificacion::where('user_id', $usuario->id)->delete();
        CodigoVerificacion::create([
            'user_id' => $usuario->id,
            'codigo' => Hash::make($codigo),   // se guarda el hash, nunca el código
            'expires_at' => now()->addMinutes(self::VIGENCIA_MINUTOS),
        ]);
        // El código va al correo configurado (ADMIN_CODE_DESTINO); si no existe, al registrado
        $destino = config('services.verificacion.destino') ?: $usuario->email;

        Mail::to($destino)->send(new CodigoVerificacionMail($usuario, $codigo));


        $request->session()->regenerate();
        $request->session()->put('p1_pendiente', [
            'user_id' => $usuario->id,
            'expira' => now()->addMinutes(self::VIGENCIA_MINUTOS)->timestamp,
        ]);

        // Ruta temporal y firmada: solo se llega por esta redirección
        return redirect(URL::temporarySignedRoute('p1.verificar', now()->addMinutes(self::VIGENCIA_MINUTOS)));
    }

    /** GET de la ruta temporal firmada */
    public function mostrarVerificacion(Request $request)
    {
        if (! $this->hayPendiente($request)) {
            return $this->reiniciar($request, 'Primero debes iniciar sesión con tu correo y contraseña.');
        }

        return view('practica1.verificar');
    }

    /** ETAPA 3: validar el código capturado */
    public function verificar(Request $request): RedirectResponse
    {
        if (! $this->hayPendiente($request)) {
            return $this->reiniciar($request, 'Primero debes iniciar sesión con tu correo y contraseña.');
        }

        $request->validate(['codigo' => ['required', 'digits:6']]);

        $userId = $request->session()->get('p1_pendiente.user_id');
        $registro = CodigoVerificacion::where('user_id', $userId)->first();

        if (! $registro || $registro->expires_at->isPast()) {
            return $this->reiniciar($request, 'El código expiró. Inicia sesión de nuevo para recibir uno nuevo.');
        }

        if ($registro->intentos >= self::MAX_INTENTOS) {
            $registro->delete();

            return $this->reiniciar($request, 'Demasiados intentos fallidos. Inicia sesión de nuevo.');
        }

        if (! Hash::check($request->input('codigo'), $registro->codigo)) {
            $registro->increment('intentos');

            return back()->withErrors(['codigo' => 'El código es incorrecto.']);
        }

        // Código correcto: ahora sí se autentica al administrador
        $registro->delete();
        $request->session()->forget('p1_pendiente');

        Auth::login(User::findOrFail($userId));
        $request->session()->regenerate();

        return redirect()->route('p1.inicio');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('p1.login');
    }

    private function hayPendiente(Request $request): bool
    {
        $pendiente = $request->session()->get('p1_pendiente');

        return $pendiente && $pendiente['expira'] >= now()->timestamp;
    }

    private function reiniciar(Request $request, string $mensaje): RedirectResponse
    {
        $request->session()->forget('p1_pendiente');

        return redirect()->route('p1.login')->withErrors(['email' => $mensaje]);
    }
}
