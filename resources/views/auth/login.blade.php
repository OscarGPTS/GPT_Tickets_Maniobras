<!DOCTYPE html>
<html lang="es" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Iniciar Sesión - Sistema de Tickets</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="mx-auto h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center">
                <i class="fas fa-boxes text-white text-2xl"></i>
            </div>
            <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                Sistema de Tickets para Movimiento de Cargas
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Gestiona tus solicitudes de movimiento de carga de manera eficiente
            </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
                
                <!-- Flash Messages -->
                @if(session('error'))
                    <div class="mb-6 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle h-5 w-5 text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-md bg-red-50 p-4">
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
                @endif

                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 text-center">
                            Inicia sesión con tu cuenta de Google
                        </h3>
                        <p class="mt-2 text-sm text-gray-600 text-center">
                            Utiliza tu cuenta corporativa de Google para acceder al sistema
                        </p>
                    </div>

                    <div class="space-y-3">
                        <!-- Opción principal: Google directo -->
                        <a href="auth/google" 
                           class="flex w-full justify-center items-center gap-3 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:ring-2 focus-visible:ring-blue-600 transition duration-150 ease-in-out">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Continuar con Google
                        </a>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <div class="text-sm text-gray-500 text-center space-y-2">
                            <p><strong>Usuarios:</strong> Pueden crear solicitudes de movimiento de carga</p>
                            <p><strong>Equipo de Almacén:</strong> Pueden gestionar y completar las solicitudes</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-gray-600">
                <p>Al iniciar sesión, aceptas nuestros términos de servicio y política de privacidad.</p>
                <p class="mt-2">Para obtener acceso al sistema, contacta con tu administrador.</p>
            </div>
        </div>
    </div>

    <!-- Loading overlay -->
    <div id="loading-overlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="mt-4 text-gray-700">Redirigiendo a Google...</p>
        </div>
    </div>

    <script>
        // Show loading overlay when Google auth link is clicked
        document.querySelector('a[href="auth/google"]').addEventListener('click', function() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        });
    </script>
</body>
</html>