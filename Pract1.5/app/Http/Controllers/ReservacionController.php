<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Reservacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservacionController extends Controller
{
    //estudiante ve sus reservaciones
    public function mias(Request $request)
    {
        $reservaciones = $request->user()->reservaciones()->with('equipo')->latest()->get();

        return view('reservaciones.mias', compact('reservaciones'));
    }

    public function todas() //todas las reservaciones
    {
        $reservaciones = Reservacion::with(['user', 'equipo'])->latest()->paginate(15);

        return view('reservaciones.todas', compact('reservaciones'));
    }

    //detalle solo el dueño o un admin
    public function show(Request $request, Reservacion $reservacion)
    {
        $this->autorizar($request, $reservacion);

        return view('reservaciones.show', ['reservacion' => $reservacion->load(['user', 'equipo'])]);
    }

    
     //Reservar, la transacción con lockForUpdate bloquea la fila del equipo: si dos usuarios
     //reservan al mismo tiempo, el segundo espera, ve el equipo ya reservado y recibe un error.
    public function reservar(Request $request, Equipo $equipo): RedirectResponse
    {
        $usuarioId = $request->user()->id;

        $reservado = DB::transaction(function () use ($equipo, $usuarioId) {
            $bloqueado = Equipo::whereKey($equipo->id)->lockForUpdate()->first();

            if (! $bloqueado || $bloqueado->estado !== Equipo::DISPONIBLE) {
                return false;
            }

            Reservacion::create([
                'user_id' => $usuarioId,
                'equipo_id' => $bloqueado->id,
                'estado' => Reservacion::ACTIVA,
            ]);
            $bloqueado->update(['estado' => Equipo::RESERVADO]);

            return true;
        });

        if (! $reservado) {
            return redirect()->route('equipos.index')->with('error', 'Ese equipo ya no está disponible. Elige otro.');
        }

        return redirect()->route('reservaciones.mias')->with('success', 'Equipo reservado correctamente.');
    }

    
     //Cancelar, condiciones: la reservación debe estar activa y pertenecer al usuario
     //el administrador puede cancelar cualquiera)
     
    public function cancelar(Request $request, Reservacion $reservacion): RedirectResponse
    {
        $this->autorizar($request, $reservacion);

        $cancelada = DB::transaction(function () use ($reservacion) {
            $actual = Reservacion::whereKey($reservacion->id)->lockForUpdate()->first();

            if ($actual->estado !== Reservacion::ACTIVA) {
                return false;
            }

            $actual->update(['estado' => Reservacion::CANCELADA, 'cancelada_at' => now()]);
            Equipo::whereKey($actual->equipo_id)
                ->where('estado', Equipo::RESERVADO)
                ->update(['estado' => Equipo::DISPONIBLE]);

            return true;
        });

        return back()->with(
            $cancelada ? 'success' : 'error',
            $cancelada ? 'Reservación cancelada; el equipo vuelve a estar disponible.' : 'Solo se pueden cancelar reservaciones activas.'
        );
    }

    private function autorizar(Request $request, Reservacion $reservacion): void
    {
        $usuario = $request->user();

        abort_unless(
            $usuario->esAdmin() || $reservacion->user_id === $usuario->id,
            403,
            'Esta reservación pertenece a otro usuario.'
        );
    }
}
