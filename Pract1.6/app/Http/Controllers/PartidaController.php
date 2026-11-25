<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartidaController extends Controller //controlador principal del gatiko
{
    public function inicio(Request $request)
    {   //muestra la sala principal (lobby) del jugador
        $yo = $request->user();
        if ($yo->esAdmin()) {
            return redirect()->route('admin.partidas.index');
        }
        // 1. partidas creadas por otros jugadores que siguen esperando rival
        $disponibles = Partida::with('jugadorX')
            ->where('estado', Partida::ESPERANDO)
            ->where('jugador_x_id', '!=', $yo->id)
            ->latest()->get();
        // 2. partidas propias que aún están abiertas (esperando rival o en curso)
        $misPartidas = Partida::with(['jugadorX', 'jugadorO'])
            ->de($yo->id)
            ->whereIn('estado', [Partida::ESPERANDO, Partida::EN_CURSO])
            ->latest()->get();
        // 3. obtiene los IDs de todos los jugadores que actualmente están dentro de una partida activa
        $ocupados = Partida::whereIn('estado', [Partida::ESPERANDO, Partida::EN_CURSO])
            ->get(['jugador_x_id', 'jugador_o_id'])
            ->flatMap(fn ($p) => [$p->jugador_x_id, $p->jugador_o_id])
            ->filter()->unique()->all();
        // 4. lista de todos los demás jugadores registrados para mostrar quién está libre u ocupado
        $jugadores = User::where('role', User::JUGADOR)
            ->where('id', '!=', $yo->id)
            ->orderBy('name')->get();
        return view('lobby', compact('disponibles', 'misPartidas', 'ocupados', 'jugadores'));
    }

    public function crear(Request $request): RedirectResponse
    {   //crea una nueva partida en estado 'esperando' y lleva al creador directo al tablero
        $partida = Partida::create(['jugador_x_id' => $request->user()->id]);
        return redirect()->route('partidas.show', $partida)
            ->with('success', 'Partida creada. Esperando a un rival…');
    }

    //uirse a una partida
    public function unirse(Request $request, Partida $partida): RedirectResponse
    {
        $yo = $request->user()->id;

        $unido = DB::transaction(function () use ($partida, $yo) {
            $p = Partida::whereKey($partida->id)->lockForUpdate()->first();
            //verifica que siga esperando rival y que el creador no intente unirse contra sí mismo
            if (! $p || $p->estado !== Partida::ESPERANDO || $p->jugador_x_id === $yo) {
                return false;
            }
            $creador = $p->jugador_x_id;
            [$x, $o] = random_int(0, 1) ? [$creador, $yo] : [$yo, $creador]; //qn llevara las 'X' y quién las 'O'

            $p->update([
                'jugador_x_id' => $x,
                'jugador_o_id' => $o,
                'estado' => Partida::EN_CURSO,
                'turno' => 'X',
            ]);

            return true;
        });

        if (! $unido) {
            return redirect()->route('inicio')->with('error', 'Esa partida ya no está disponible.');
        }

        return redirect()->route('partidas.show', $partida);
    }

    //solo los participantes (y el administrador, en modo lectura)
    public function show(Request $request, Partida $partida)
    {  //muestra la vista del tablero de una partida
        $yo = $request->user();
        abort_unless($yo->esAdmin() || $partida->participa($yo->id), 403, 'No tienes acceso a esta partida.');
        $partida->load(['jugadorX', 'jugadorO', 'ganador']);
        $miSimbolo = $partida->simboloDe($yo->id);
        $esMiTurno = $partida->estado === Partida::EN_CURSO && $partida->jugadorEnTurnoId() === $yo->id;
        //prepara el mensaje de estado que verá el usuario encima del tablero
        $mensaje = match (true) {
            $partida->estado === Partida::ESPERANDO => 'Esperando a que un rival se una…',
            $partida->estado === Partida::CANCELADA => 'La partida fue cancelada.',
            $partida->estado === Partida::TERMINADA && $partida->resultado === 'empate' => '¡Empate!',
            $partida->estado === Partida::TERMINADA => $partida->ganador_id === $yo->id
                ? '¡Ganaste!'
                : ($miSimbolo ? 'Perdiste.' : 'Ganó '.$partida->ganador?->name),
            $esMiTurno => 'Es tu turno.',
            default => 'Turno del rival…',
        };

        //la página se recarga sola mientras esperamos al rival en mi turno no, para no interrumpirme
        $refrescar = $partida->enJuego() && ! $esMiTurno;

        return view('partida', compact('partida', 'miSimbolo', 'esMiTurno', 'mensaje', 'refrescar'));
    }

    /**
     * todo se valida en el servidor dentro de una transacción con lockForUpdate():
     * si los dos jugadores mueven a la vez, las peticiones se atienden una por una.
     */
    public function mover(Request $request, Partida $partida): RedirectResponse
    {
        $datos = $request->validate(['posicion' => ['required', 'integer', 'between:0,8']]);
        $yo = $request->user();
        //validaciones y reglas del juefo
        $error = DB::transaction(function () use ($partida, $datos, $yo) {
            $p = Partida::whereKey($partida->id)->lockForUpdate()->firstOrFail();

            if (! $p->participa($yo->id)) {
                return 'No participas en esta partida.';
            }
            if ($p->estado !== Partida::EN_CURSO) {
                return 'La partida no está en curso.';
            }
            if ($p->jugadorEnTurnoId() !== $yo->id) {
                return 'No es tu turno.';
            }

            $casillas = $p->casillas();
            $pos = (int) $datos['posicion'];

            if ($casillas[$pos] !== '-') {
                return 'Esa casilla ya está ocupada.';
            }
            //la casilla en el tablero y guarda el historial
            $simbolo = $p->turno;
            $casillas[$pos] = $simbolo;
            $p->tablero = implode('', $casillas);

            Movimiento::create([
                'partida_id' => $p->id,
                'user_id' => $yo->id,
                'posicion' => $pos,
                'simbolo' => $simbolo,
            ]);
            //revisa si con esta jugada hubo ganador, empate o se rinde
            if (Partida::calcularGanador($casillas)) {
                $p->estado = Partida::TERMINADA;
                $p->resultado = 'victoria';
                $p->ganador_id = $yo->id;
            } elseif (! in_array('-', $casillas, true)) {
                $p->estado = Partida::TERMINADA;
                $p->resultado = 'empate';
            } else {
                $p->turno = $simbolo === 'X' ? 'O' : 'X';
            }

            $p->save();

            return null;
        });

        return redirect()->route('partidas.show', $partida)
            ->with($error ? 'error' : 'success', $error ?? 'Movimiento registrado.');
    }
    //permite a un jugador salir de una partida activa
    public function abandonar(Request $request, Partida $partida): RedirectResponse
    {
        $yo = $request->user()->id;

        DB::transaction(function () use ($partida, $yo) {
            $p = Partida::whereKey($partida->id)->lockForUpdate()->firstOrFail();

            abort_unless($p->participa($yo), 403);

            if ($p->estado === Partida::ESPERANDO) {
                $p->update(['estado' => Partida::CANCELADA, 'resultado' => 'cancelada']);
            } elseif ($p->estado === Partida::EN_CURSO) {
                $rival = $p->jugador_x_id === $yo ? $p->jugador_o_id : $p->jugador_x_id;
                $p->update(['estado' => Partida::TERMINADA, 'resultado' => 'abandono', 'ganador_id' => $rival]);
            }
        });

        return redirect()->route('inicio')->with('success', 'Abandonaste la partida.');
    }

    //hstorial de partidas terminadas o canceladas del jugador
    public function historial(Request $request)
    {
        $partidas = Partida::with(['jugadorX', 'jugadorO'])
            ->de($request->user()->id)
            ->whereIn('estado', [Partida::TERMINADA, Partida::CANCELADA])
            ->latest()->paginate(10);

        return view('historial', compact('partidas'));
    }
}
