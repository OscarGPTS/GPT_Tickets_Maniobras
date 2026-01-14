@extends('layouts.app')

@section('title', 'Crear Nuevo Usuario')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Usuario</h1>
                        <p class="text-gray-600 mt-2">Agrega un nuevo usuario al sistema y asigna roles</p>
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


        <!-- Formulario de Creación -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Información del Nuevo Usuario</h3>
                <p class="text-sm text-gray-600 mt-1">Completa todos los campos requeridos para crear el usuario</p>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="p-6" onsubmit="return validateForm();">
                @csrf

           
                <div class="mb-6 p-4 border-2 border-stone-300 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <div class="flex-1">
                            <label for="quickSelect" class="block text-sm font-semibold text-gray-900 mb-2">
                                Buscar usuario
                            </label>
                            <p class="text-xs text-gray-600 mb-3">Selecciona un usuario existente para autocompletar sus datos</p>
                            <div id="loadingUsers" class="flex items-center justify-center py-2 text-sm text-gray-600">
                            </div>
                            <select id="quickSelect" 
                                onchange="fillFormFromUser(this.value)"
                                class="hidden w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="">-- Seleccionar usuario --</option>
                            </select>
                            <input type="hidden" id="externalIdField" name="external_id">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               placeholder="Juan Pérez"
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
                               value="{{ old('email') }}"
                               placeholder="juan@example.com"
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
                                       {{ in_array($role, old('roles', [])) ? 'checked' : '' }}
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

                <!-- Botones de Acción -->
                <div class="mt-8 flex gap-4 border-t border-gray-200 pt-6">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Crear Usuario
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 font-medium text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Información Útil -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 9a1 1 0 000 2h6a1 1 0 100-2H8zm0 4a1 1 0 000 2h3a1 1 0 100-2H8z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Información sobre usuarios y autenticación
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Alta Rápida:</strong> Los usuarios del sistema principal se cargan automáticamente al abrir esta página.</li>
                            <li><strong>Autenticación:</strong> Los usuarios se autentican con Auth0/Google, no requieren contraseña.</li>
                            <li><strong>Administrador:</strong> Acceso completo al panel, gestión de usuarios, tickets y reportes.</li>
                            <li><strong>Almacén:</strong> Puede ver, actualizar y completar tickets asignados a él.</li>
                            <li><strong>Solicitante:</strong> Puede crear nuevas solicitudes (tickets) y ver su estado.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let allUsers = [];

    // Cargar usuarios automáticamente al cargar la página
    window.addEventListener('DOMContentLoaded', function() {
        loadExternalUsers();
    });

    async function loadExternalUsers() {
        const select = document.getElementById('quickSelect');
        const loadingDiv = document.getElementById('loadingUsers');
        
        try {
            const response = await fetch('{{ route('admin.users.fetch-external') }}');
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al cargar usuarios');
            }
            
            allUsers = data.data;
            updateUserSelect(allUsers);
            
            // Ocultar loading y mostrar select
            loadingDiv.classList.add('hidden');
            select.classList.remove('hidden');
            
        } catch (error) {
            loadingDiv.innerHTML = `
                <div class="text-red-600 text-sm">
                    <svg class="inline h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Error: ${error.message}
                </div>
            `;
        }
    }

    function updateUserSelect(users) {
        const select = document.getElementById('quickSelect');
        select.innerHTML = '<option value="">-- Seleccionar usuario --</option>';
        
        users.forEach(user => {
            const option = document.createElement('option');
            option.value = JSON.stringify(user);
            option.textContent = `${user.nombre_completo} - ${user.email || 'Sin email'} (${user.puesto?.nombre || 'N/A'})`;
            select.appendChild(option);
        });
    }

    function fillFormFromUser(jsonUser) {
        if (!jsonUser) return;
        
        try {
            const user = JSON.parse(jsonUser);
            
            // Rellenar campos
            document.getElementById('name').value = user.nombre_completo || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('externalIdField').value = user.id || '';
            
            // Highlight visual
            const nameField = document.getElementById('name');
            const emailField = document.getElementById('email');
            
            nameField.classList.add('bg-green-50', 'border-green-300');
            emailField.classList.add('bg-green-50', 'border-green-300');
            
            setTimeout(() => {
                nameField.classList.remove('bg-green-50', 'border-green-300');
                emailField.classList.remove('bg-green-50', 'border-green-300');
            }, 2000);
            
            // Scroll suave hacia los campos
            document.getElementById('name').scrollIntoView({ behavior: 'smooth', block: 'center' });
            
        } catch (error) {
            console.error('Error al procesar usuario:', error);
        }
    }

    function validateForm() {
        const roles = document.querySelectorAll('input[name="roles[]"]:checked');
        if (roles.length === 0) {
            alert('Debes seleccionar al menos un rol para el usuario.');
            return false;
        }

        return true;
    }

    // Deshabilitar búsqueda inicial hasta que carguen los usuarios
    document.getElementById('searchUsers').disabled = true;
</script>
@endsection
