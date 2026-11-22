<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipoController extends Controller
{ //admin ve los equipos disponibles
    public function index(Request $request)
    {
        $consulta = Equipo::query()->orderBy('codigo');

        if (! $request->user()->esAdmin()) {
            $consulta->where('estado', Equipo::DISPONIBLE);
        }

        return view('equipos.index', ['equipos' => $consulta->get()]);
    }
//solo admin
    public function create()
    {
        return view('equipos.form', ['equipo' => new Equipo(['estado' => Equipo::DISPONIBLE])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:equipos,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'estado' => ['required', Rule::in([Equipo::DISPONIBLE, Equipo::MANTENIMIENTO])],
        ]);

        Equipo::create($datos);

        return redirect()->route('equipos.index')->with('success', 'Equipo registrado.');
    }

    public function edit(Equipo $equipo)
    {
        return view('equipos.form', compact('equipo'));
    }

    public function update(Request $request, Equipo $equipo): RedirectResponse
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:30', Rule::unique('equipos', 'codigo')->ignore($equipo->id)],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'estado' => ['nullable', Rule::in([Equipo::DISPONIBLE, Equipo::MANTENIMIENTO])],
        ]);

        //el estado reservado no se puede pisar desde el formulario
        if ($equipo->estado === Equipo::RESERVADO || empty($datos['estado'])) {
            unset($datos['estado']);
        }

        $equipo->update($datos);

        return redirect()->route('equipos.index')->with('success', 'Equipo actualizado.');
    }

    public function destroy(Equipo $equipo): RedirectResponse
    {
        if ($equipo->reservaciones()->where('estado', 'activa')->exists()) {
            return back()->with('error', 'No se puede eliminar un equipo con una reservación activa. Cancela la reservación primero.');
        }

        $equipo->delete();

        return redirect()->route('equipos.index')->with('success', 'Equipo eliminado.');
    }
}