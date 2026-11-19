<?php

namespace App\Http\Requests;

use App\Models\Alumno;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Doble protección: además del middleware role:profesor en las rutas
        return $this->user()?->role === 'profesor';
    }

    public function rules(): array
    {
        $alumno = $this->route('alumno');

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => [$alumno ? 'nullable' : 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($alumno?->user_id)],
            'password' => [$alumno ? 'nullable' : 'required', 'string', 'min:8'],
            'faltas' => ['required', 'integer', 'min:0', 'max:'.Alumno::MAX_FALTAS],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:'.Alumno::MAX_CALIFICACION],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del alumno es obligatorio.',
            'email.required' => 'El correo es obligatorio (el alumno lo usará para iniciar sesión).',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Ese correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'faltas.required' => 'El número de faltas es obligatorio.',
            'faltas.integer' => 'Las faltas deben ser un número entero.',
            'faltas.min' => 'Las faltas no pueden ser negativas.',
            'faltas.max' => 'Las faltas no pueden ser más de '.Alumno::MAX_FALTAS.'.',
            'calificacion.required' => 'La calificación es obligatoria.',
            'calificacion.numeric' => 'La calificación debe ser numérica.',
            'calificacion.min' => 'La calificación mínima es 0.',
            'calificacion.max' => 'La calificación máxima es '.Alumno::MAX_CALIFICACION.'.',
        ];
    }
}
