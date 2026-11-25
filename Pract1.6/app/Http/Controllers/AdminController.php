<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //todas las partidas, en cualquier estado
    public function partidas()
    {
        $partidas = Partida::with(['jugadorX', 'jugadorO'])->latest()->paginate(15);

        return view('admin.partidas', compact('partidas'));
    }

    //historial general: partidas terminadas o canceladas de todos los jugadores
    public function historial()
    {
        $partidas = Partida::with(['jugadorX', 'jugadorO', 'ganador'])
            ->whereIn('estado', [Partida::TERMINADA, Partida::CANCELADA])
            ->latest()->paginate(15);

        return view('admin.historial', compact('partidas'));
    }

    //Cancelar una partida en curso (o en espera). Se bloquea el registro para no chocar con un movimiento
    public function cancelar(Partida $partida): RedirectResponse
    {
        $cancelada = DB::transaction(function () use ($partida) {
            $p = Partida::whereKey($partida->id)->lockForUpdate()->firstOrFail();

            if (! $p->enJuego()) {
                return false;
            }

            $p->update(['estado' => Partida::CANCELADA, 'resultado' => 'cancelada']);

            return true;
        });

        return back()->with(
            $cancelada ? 'success' : 'error',
            $cancelada ? 'Partida cancelada.' : 'Solo se pueden cancelar partidas en espera o en curso.'
        );
    }
}
