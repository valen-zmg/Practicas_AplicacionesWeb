<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlumnoRequest;
use App\Models\Alumno;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AlumnoController extends Controller
{
    /** Redirige según el rol */
    public function inicio(Request $request): RedirectResponse
    {
        return $request->user()->esProfesor()
            ? redirect()->route('alumnos.index')
            : redirect()->route('mi');
    }

    /** Alumno: consulta únicamente su propia información (solo lectura) */
    public function miInformacion(Request $request)
    {
        $alumno = Alumno::where('user_id', $request->user()->id)->firstOrFail();

        return view('mi-informacion', compact('alumno'));
    }

    /** Profesor: listado con ordenamiento por nombre, calificación o faltas */
    public function index(Request $request)
    {
        // Lista blanca: nunca se ordena por una columna que venga libre de la URL
        $orden = in_array($request->query('orden'), Alumno::ORDENES, true) ? $request->query('orden') : 'nombre';
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $alumnos = Alumno::orderBy($orden, $dir)->orderBy('id')->paginate(15)->withQueryString();

        return view('alumnos.index', compact('alumnos'));
    }

    public function create()
    {
        return view('alumnos.form', ['alumno' => new Alumno()]);
    }

    public function store(AlumnoRequest $request): RedirectResponse
    {
        $d = $request->validated();

        DB::transaction(function () use ($d) {
            $user = User::create([
                'name' => $d['nombre'],
                'email' => $d['email'],
                'password' => $d['password'],
                'role' => User::ALUMNO,
            ]);

            Alumno::create(['user_id' => $user->id] + Arr::only($d, ['nombre', 'faltas', 'calificacion']));
        });

        return redirect()->route('alumnos.index')->with('success', 'Alumno registrado correctamente.');
    }

    public function edit(Alumno $alumno)
    {
        return view('alumnos.form', compact('alumno'));
    }

    public function update(AlumnoRequest $request, Alumno $alumno): RedirectResponse
    {
        $d = $request->validated();

        DB::transaction(function () use ($d, $alumno) {
            $alumno->update(Arr::only($d, ['nombre', 'faltas', 'calificacion']));

            if ($alumno->user) {
                $cambios = ['name' => $d['nombre']];
                if (! empty($d['email'])) {
                    $cambios['email'] = $d['email'];
                }
                if (! empty($d['password'])) {
                    $cambios['password'] = $d['password'];
                }
                $alumno->user->update($cambios);
            }
        });

        return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado correctamente.');
    }

    public function destroy(Alumno $alumno): RedirectResponse
    {
        DB::transaction(function () use ($alumno) {
            $user = $alumno->user;
            $alumno->delete();
            $user?->delete();
        });

        return redirect()->route('alumnos.index')->with('success', 'Registro eliminado.');
    }
}
