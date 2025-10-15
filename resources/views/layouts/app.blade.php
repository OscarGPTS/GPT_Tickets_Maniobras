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
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
        
        /* Smooth transitions */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Navigation -->
        <x-header />

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
    
    @stack('scripts')
</body>
</html>
