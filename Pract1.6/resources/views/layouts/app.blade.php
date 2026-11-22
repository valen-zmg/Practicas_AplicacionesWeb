<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo') · Juego del gato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('head')
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Práctica 1.6 · Juego del gato</span>
        @auth
            <div class="d-flex align-items-center gap-2 text-white small">
                <span class="badge text-bg-secondary">{{ auth()->user()->role }}</span>
                <span class="text-white-50" title="Identificador de sesión (parcial)">sesión {{ substr(session()->getId(), 0, 8) }}</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-light">Cerrar sesión</button>
                </form>
            </div>
        @endauth
    </div>
</nav>
<main class="container pb-5">
    @auth
        <ul class="nav nav-pills mb-3">
            @if(auth()->user()->esAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.partidas.*', 'partidas.show') ? 'active' : '' }}" href="{{ route('admin.partidas.index') }}">Partidas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.historial') ? 'active' : '' }}" href="{{ route('admin.historial') }}">Historial general</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">Usuarios</a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('inicio', 'partidas.show') ? 'active' : '' }}" href="{{ route('inicio') }}">Lobby</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('historial') ? 'active' : '' }}" href="{{ route('historial') }}">Mi historial</a>
                </li>
            @endif
        </ul>
    @endauth

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @yield('contenido')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>