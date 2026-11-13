@extends('layouts.app')
@section('titulo', 'Inicio')
@section('contenido')
<div class="alert alert-success">
    Sesión iniciada como <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }}).
</div>
@endsection