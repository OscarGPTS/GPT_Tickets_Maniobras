<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Sistema de Tickets')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    <!-- Forzar carga de algunas clases esenciales de Tailwind para evitar purging -->
    <style>
        /* Asegurar que las clases esenciales estén siempre disponibles */
        .hidden { display: none !important; }
        .block { display: block !important; }
        .flex { display: flex !important; }
        .grid { display: grid !important; }
        .sr-only { position: absolute !important; width: 1px !important; height: 1px !important; padding: 0 !important; margin: -1px !important; overflow: hidden !important; clip: rect(0, 0, 0, 0) !important; white-space: nowrap !important; border-width: 0 !important; }
        .relative { position: relative !important; }
        .absolute { position: absolute !important; }
        .fixed { position: fixed !important; }
        .z-50 { z-index: 50 !important; }
    </style>
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-indigo-600">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-white text-xl font-bold">Sistema de Tickets</h1>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                @auth
                                    <a href="{{ route('dashboard') }}" 
                                       class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('dashboard') ? 'bg-indigo-700' : '' }}">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('tickets.index') }}" 
                                       class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('tickets.*') ? 'bg-indigo-700' : '' }}">
                                        Mis Tickets
                                    </a>
                                    <a href="{{ route('surveys.index') }}" 
                                       class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('surveys.*') ? 'bg-indigo-700' : '' }}">
                                        Encuestas
                                    </a>
                                    @if(auth()->user()->roles()->where('name', 'almacen')->exists())
                                        <a href="{{ route('almacen.dashboard') }}" 
                                           class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('almacen.*') ? 'bg-indigo-700' : '' }}">
                                            Panel Almacén
                                        </a>
                                    @endif
                                    @if(auth()->user()->roles()->where('name', 'admin')->exists())
                                        <a href="{{ route('admin.dashboard') }}" 
                                           class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('admin.*') ? 'bg-indigo-700' : '' }}">
                                            Administración
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            @auth
                                <!-- Notifications -->
                                <a href="{{ route('notifications.index') }}" class="relative rounded-full bg-indigo-600 p-1 text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600">
                                    <span class="absolute -inset-1.5"></span>
                                    <span class="sr-only">Ver notificaciones</span>
                                    <i class="fas fa-bell h-6 w-6"></i>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                            {{ auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </a>

                                <!-- Profile dropdown -->
                                <div class="relative ml-3">
                                    <div>
                                        <button type="button" class="relative flex max-w-xs items-center rounded-full bg-indigo-600 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                            <span class="absolute -inset-1.5"></span>
                                            <span class="sr-only">Abrir menú de usuario</span>
                                            @if(auth()->user()->avatar)
                                                <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->getAvatarUrl() }}" alt="{{ auth()->user()->name }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center">
                                                    <span class="text-white font-medium text-sm">{{ auth()->user()->getInitials() }}</span>
                                                </div>
                                            @endif
                                        </button>
                                    </div>
                                    <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="user-menu">
                                        <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                            <div class="font-medium">{{ auth()->user()->name }}</div>
                                            <div class="text-gray-500">{{ auth()->user()->email }}</div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                <i class="fas fa-user-tag mr-1"></i>{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Sin roles' }}
                                            </div>
                                        </div>
                                        <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                            <i class="fas fa-bell mr-2"></i>Notificaciones
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-400">
                                    Iniciar Sesión
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Header -->
        @if(isset($header))
            <header class="bg-white shadow">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Main Content -->
        <main>
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle h-5 w-5 text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle h-5 w-5 text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle h-5 w-5 text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Errores encontrados:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul role="list" class="list-disc space-y-1 pl-5">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- JavaScript for dropdown -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');

            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function() {
                    userMenu.classList.toggle('hidden');
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
