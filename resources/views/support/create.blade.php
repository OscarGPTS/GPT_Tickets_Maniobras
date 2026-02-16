<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Solicitud de Soporte - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header con Logo -->
            <div class="text-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center mb-6">
                    <img src="{{ asset('storage/img/logo.png') }}" alt="Logo" style="width: 80px;">
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Centro de Soporte
                </h1>
                <p class="text-gray-600">
                    Estamos aquí para ayudarte. Completa el formulario y nos pondremos en contacto contigo.
                </p>
            </div>

            <!-- Mensajes de éxito/error -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-md p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
                        <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-md p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                        <p class="text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Formulario -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-8 sm:px-10">
                    <form action="{{ route('support.store') }}" method="POST">
                        @csrf

                        <!-- Nombre Completo -->
                        <div class="mb-6">
                            <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user text-indigo-500 mr-2"></i>
                                Nombre Completo <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="full_name" 
                                id="full_name" 
                                value="{{ old('full_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('full_name') border-red-500 @enderror"
                                placeholder="Tu nombre completo"
                                required
                            >
                            @error('full_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Correo Electrónico -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope text-indigo-500 mr-2"></i>
                                Correo Electrónico <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('email') border-red-500 @enderror"
                                placeholder="tu@email.com"
                                required
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de Solicitud -->
                        <div class="mb-6">
                            <label for="request_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-list text-indigo-500 mr-2"></i>
                                Tipo de Solicitud <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="request_type" 
                                id="request_type" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('request_type') border-red-500 @enderror"
                                required
                            >
                                <option value="">Selecciona una opción</option>
                                <option value="eliminar_datos" {{ old('request_type') == 'eliminar_datos' ? 'selected' : '' }}>
                                    Eliminación de Datos Personales (GDPR/CCPA)
                                </option>
                                <option value="recuperar_contrasena" {{ old('request_type') == 'recuperar_contrasena' ? 'selected' : '' }}>
                                    Recuperar Contraseña
                                </option>
                                <option value="cambiar_email" {{ old('request_type') == 'cambiar_email' ? 'selected' : '' }}>
                                    Cambiar Correo Electrónico
                                </option>
                                <option value="consulta_general" {{ old('request_type') == 'consulta_general' ? 'selected' : '' }}>
                                    Consulta General
                                </option>
                                <option value="otro" {{ old('request_type') == 'otro' ? 'selected' : '' }}>
                                    Otro
                                </option>
                            </select>
                            @error('request_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-comment-dots text-indigo-500 mr-2"></i>
                                Descripción de tu Solicitud <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="description" 
                                id="description" 
                                rows="6"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('description') border-red-500 @enderror"
                                placeholder="Por favor, describe tu solicitud con el mayor detalle posible..."
                                required
                            >{{ old('description') }}</textarea>
                            <p class="mt-2 text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Máximo 2000 caracteres
                            </p>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Aviso de Privacidad -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-shield-alt text-blue-600 mt-1 mr-3"></i>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold mb-1">Privacidad y Seguridad</p>
                                    <p>
                                        Tu información será tratada de forma confidencial y utilizada únicamente para procesar tu solicitud. 
                                        Nos comprometemos a proteger tus datos personales según las regulaciones aplicables.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button 
                                type="submit"
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <i class="fas fa-paper-plane mr-2"></i>
                                Enviar Solicitud
                            </button>
                            <a 
                                href="{{ url('/') }}"
                                class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors"
                            >
                                <i class="fas fa-arrow-left mr-2"></i>
                                Volver al Inicio
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Footer del Formulario -->
                <div class="bg-gray-50 px-6 py-4 sm:px-10 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-gray-600">
                        <div class="flex items-center mb-2 sm:mb-0">
                            <i class="fas fa-clock text-gray-400 mr-2"></i>
                            <span>Tiempo de respuesta: 24-48 horas</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-headset text-gray-400 mr-2"></i>
                            <span>Soporte disponible 24/7</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-full mb-4">
                        <i class="fas fa-envelope text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Email</h3>
                    <p class="text-sm text-gray-600">ochavez@gptservices.com</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-phone text-green-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Teléfono</h3>
                    <p class="text-sm text-gray-600">+52 5565235522</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mb-4">
                        <i class="fas fa-comment text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Horario</h3>
                    <p class="text-sm text-gray-600">Lun-Vie 7:30 am - 3:00 pm</p>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
