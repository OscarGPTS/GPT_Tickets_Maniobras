@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Editar Usuario</h1>
                        <p class="text-gray-600 mt-2">Modifica la información y rol del usuario</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Usuarios
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Usuario -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div class="ml-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <div class="mt-2">
                            <div class="flex flex-wrap gap-2">
                                @forelse($user->roles as $role)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($role->name === 'admin') bg-red-100 text-red-800
                                        @elseif($role->name === 'almacen') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @empty
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Sin roles asignados
                                    </span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="text-sm text-gray-500">Registrado el</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                        <p class="text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes de Error -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Se encontraron los siguientes errores:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulario de Edición -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Editar Información del Usuario</h3>
                <p class="text-sm text-gray-600 mt-1">Actualiza los datos del usuario y asigna el rol apropiado</p>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6" onsubmit="return validateForm();">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Roles -->
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Roles del Usuario <span class="text-red-500">*</span>
                    </label>
                    <p class="text-sm text-gray-600 mb-4">Selecciona uno o más roles para el usuario</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        @foreach($roles as $role)
                            <div class="relative">
                                <input type="checkbox" 
                                       id="role_{{ $role }}" 
                                       name="roles[]" 
                                       value="{{ $role }}" 
                                       {{ $user->hasRole($role) ? 'checked' : '' }}
                                       class="peer sr-only">
                                <label for="role_{{ $role }}" class="flex flex-col items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                    <span class="text-sm font-medium text-gray-900">
                                        @if($role === 'admin')
                                            <span class="flex items-center">
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-800 mr-2">
                                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.429 5.951 1.429a1 1 0 001.169-1.409l-7-14z"/>
                                                    </svg>
                                                </span>
                                                Administrador
                                            </span>
                                        @elseif($role === 'almacen')
                                            <span class="flex items-center">
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 text-blue-800 mr-2">
                                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                                    </svg>
                                                </span>
                                                Almacén
                                            </span>
                                        @else
                                            <span class="flex items-center">
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-800 mr-2">
                                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                                Solicitante
                                            </span>
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-600 mt-2">
                                        @if($role === 'admin')
                                            Acceso completo al panel administrativo
                                        @elseif($role === 'almacen')
                                            Gestiona tickets y entregas
                                        @else
                                            Puede crear y ver sus solicitudes
                                        @endif
                                    </span>
                                </label>
                            </div>
                        @endforeach

                    </div>
                    @error('roles')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Información Adicional -->
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Información sobre los roles</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>Administrador:</strong> Puede gestionar usuarios, asignar roles y ver estadísticas completas</li>
                                    <li><strong>Almacén:</strong> Puede gestionar tickets, asignarlos y marcarlos como completados</li>
                                    <li><strong>Solicitante:</strong> Puede crear tickets y responder encuestas de satisfacción</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advertencias -->
                @if($user->hasRole('admin') && \App\Models\User::role('admin')->count() <= 1)
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-yellow-800">¡Advertencia!</h4>
                                <p class="mt-1 text-sm text-yellow-700">
                                    Este es el único administrador del sistema. Asegúrate de que siempre haya al menos un administrador activo.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->id === auth()->id())
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">Nota</h4>
                                <p class="mt-1 text-sm text-blue-700">
                                    Estás editando tu propia cuenta. Los cambios se aplicarán inmediatamente.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Botones -->
                <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
    function validateForm() {
        const roles = document.querySelectorAll('input[name="roles[]"]:checked');
        if (roles.length === 0) {
            alert('Debes seleccionar al menos un rol para el usuario.');
            return false;
        }
        return true;
    }
</script>
@endsection