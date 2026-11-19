@extends('layouts.app')
@section('titulo', 'Mi información académica')
@section('contenido')
<h1 class="h3 mb-3">Mi información académica</h1>
<div class="card shadow-sm">
    <table class="table mb-0 align-middle">
        <thead>
            <tr><th>Nombre</th><th>Faltas</th><th>Calificación</th><th>Equivalencia</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $alumno->nombre }}</td>
                <td>{{ $alumno->faltas }}</td>
                <td>{{ number_format($alumno->calificacion, 2) }}</td>
                <td>
                    <span class="badge text-bg-{{ $alumno->color_equivalencia }}">{{ $alumno->equivalencia }}</span>
                    {{ $alumno->equivalencia_nombre }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
<p class="text-muted small mt-2">Solo puedes consultar tu información. No puedes registrar, modificar ni eliminar.</p>
@endsection