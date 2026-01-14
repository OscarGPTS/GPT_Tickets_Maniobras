@extends('layouts.app')

@section('title', 'Alta Rápida de Usuarios')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Alta Rápida de Usuarios</h1>
                        <p class="text-gray-600 mt-2">Importa usuarios del sistema principal de manera rápida</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Panel de búsqueda -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Buscar Usuarios Disponibles</h3>
                        <p class="text-sm text-gray-600 mt-1">Selecciona un usuario del sistema principal</p>
                    </div>

                    <div class="p-6">
                        <!-- Campo de búsqueda -->
                        <div class="mb-6">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                Buscar por nombre, email o cédula
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="search" 
                                       placeholder="Ej: Juan Pérez, juan@email.com"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Lista de usuarios -->
                        <div id="usersList" class="space-y-3 max-h-96 overflow-y-auto">
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                                <p class="text-gray-500">Escribe para buscar usuarios...</p>
                            </div>
                        </div>

                        <!-- Indicador de carga -->
                        <div id="loadingIndicator" class="hidden text-center py-4">
                            <div class="inline-block">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 mt-2 text-sm">Buscando usuarios...</p>
                        </div>

                        <!-- Mensaje de error -->
                        <div id="errorMessage" class="hidden bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div class="ml-3">
                                    <p id="errorText" class="text-sm font-medium text-red-800"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de información y roles seleccionados -->
            <div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-8">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Usuario Seleccionado</h3>
                    </div>

                    <div class="p-6">
                        <div id="selectedUserInfo" class="space-y-4">
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                <p class="text-sm">Selecciona un usuario</p>
                            </div>
                        </div>

                        <!-- Formulario oculto de roles -->
                        <form id="quickUserForm" class="hidden mt-6 space-y-4" onsubmit="submitQuickUser(event);">
                            <input type="hidden" id="externalId" name="external_id">
                            <input type="hidden" id="nombreCompleto" name="nombre_completo">
                            <input type="hidden" id="email" name="email">

                            <div class="border-t pt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Asignar Roles <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-2">
                                    @foreach($roles as $role)
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   id="role_{{ $role }}" 
                                                   name="roles[]" 
                                                   value="{{ $role }}"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="role_{{ $role }}" class="ml-2 block text-sm text-gray-700">
                                                <span class="font-medium">{{ ucfirst($role) }}</span>
                                                <span class="text-gray-500 text-xs block">
                                                    @if($role === 'admin')
                                                        Acceso completo
                                                    @elseif($role === 'almacen')
                                                        Gestión de tickets
                                                    @else
                                                        Crear solicitudes
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <p id="rolesError" class="hidden text-sm text-red-600 mt-2"></p>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mt-4">
                                <p class="text-xs text-blue-800">
                                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Se generará una contraseña aleatoria que será mostrada después de crear el usuario.
                                </p>
                            </div>

                            <button type="submit" class="w-full mt-4 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Crear Usuario
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal de éxito -->
        <div id="successModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="text-center">
                    <svg class="h-16 w-16 text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">¡Usuario Creado Exitosamente!</h3>
                    
                    <div id="successContent" class="text-left space-y-3 mb-4 bg-gray-50 p-4 rounded">
                        <div>
                            <p class="text-sm text-gray-600">Nombre:</p>
                            <p id="successName" class="font-semibold text-gray-900"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email:</p>
                            <p id="successEmail" class="font-semibold text-gray-900"></p>
                        </div>
                        <div class="border-t pt-3">
                            <p class="text-sm text-gray-600 mb-1">Contraseña Temporal:</p>
                            <div class="flex items-center space-x-2">
                                <code id="successPassword" class="bg-yellow-50 border border-yellow-200 rounded px-3 py-2 text-sm font-mono text-gray-900 flex-1"></code>
                                <button type="button" onclick="copyPassword()" class="px-3 py-2 bg-blue-100 text-blue-700 rounded text-sm font-medium hover:bg-blue-200">
                                    Copiar
                                </button>
                            </div>
                            <p class="text-xs text-yellow-700 mt-2">
                                <strong>Importante:</strong> El usuario debe cambiar esta contraseña en su primer acceso.
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Roles Asignados:</p>
                            <div id="successRoles" class="flex flex-wrap gap-2 mt-1"></div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('admin.users.index') }}" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">
                            Ir a Usuarios
                        </a>
                        <button type="button" onclick="resetForm()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-900 rounded-md hover:bg-gray-400 font-medium">
                            Crear Otro
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const searchInput = document.getElementById('search');
    const usersList = document.getElementById('usersList');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const selectedUserInfo = document.getElementById('selectedUserInfo');
    const quickUserForm = document.getElementById('quickUserForm');
    const successModal = document.getElementById('successModal');
    let debounceTimer;

    // Event listener para búsqueda
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const searchValue = this.value.trim();

        if (searchValue.length < 2) {
            usersList.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <p class="text-gray-500">Escribe para buscar usuarios...</p>
                </div>
            `;
            errorMessage.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchUsers(searchValue);
        }, 500);
    });

    async function fetchUsers(search) {
        loadingIndicator.classList.remove('hidden');
        errorMessage.classList.add('hidden');
        usersList.innerHTML = '';

        try {
            const response = await fetch(`{{ route('admin.users.fetch-external') }}?search=${encodeURIComponent(search)}`);
            const data = await response.json();

            loadingIndicator.classList.add('hidden');

            if (!data.success) {
                throw new Error(data.message || 'Error al obtener usuarios');
            }

            if (data.data.length === 0) {
                usersList.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>No se encontraron usuarios</p>
                    </div>
                `;
                return;
            }

            // Mostrar usuarios
            usersList.innerHTML = data.data.map(user => `
                <div onclick="selectUser(${user.id}, '${user.nombre_completo}', '${user.email || 'No disponible'}')" 
                     class="p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-colors">
                    <div class="flex items-start space-x-4">
                        <img src="${user.foto_perfil || 'https://via.placeholder.com/48'}" 
                             alt="${user.nombre_completo}" 
                             class="w-12 h-12 rounded-full flex-shrink-0 object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">${user.nombre_completo}</p>
                            <p class="text-sm text-gray-600 truncate">${user.email || 'Sin email'}</p>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                <span class="inline-flex px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                    ${user.puesto?.nombre || 'N/A'}
                                </span>
                                <span class="inline-flex px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                    ${user.departamento?.nombre || 'N/A'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

        } catch (error) {
            loadingIndicator.classList.add('hidden');
            errorMessage.classList.remove('hidden');
            errorText.textContent = error.message || 'Error al buscar usuarios';
        }
    }

    function selectUser(id, nombreCompleto, email) {
        // Mostrar información del usuario seleccionado
        selectedUserInfo.innerHTML = `
            <div class="text-center pb-4">
                <div class="inline-block w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center mb-4">
                    <span class="text-white text-xl font-bold">${nombreCompleto.charAt(0).toUpperCase()}</span>
                </div>
                <h4 class="font-semibold text-gray-900 text-lg">${nombreCompleto}</h4>
                <p class="text-sm text-gray-600">${email}</p>
            </div>
        `;

        // Establecer valores en el formulario
        document.getElementById('externalId').value = id;
        document.getElementById('nombreCompleto').value = nombreCompleto;
        document.getElementById('email').value = email;

        // Limpiar selección de roles
        document.querySelectorAll('input[name="roles[]"]').forEach(el => el.checked = false);

        // Mostrar formulario
        selectedUserInfo.appendChild(quickUserForm);
        quickUserForm.classList.remove('hidden');
    }

    async function submitQuickUser(event) {
        event.preventDefault();

        // Validar que al menos un rol está seleccionado
        const selectedRoles = document.querySelectorAll('input[name="roles[]"]:checked');
        if (selectedRoles.length === 0) {
            document.getElementById('rolesError').textContent = 'Debes seleccionar al menos un rol';
            document.getElementById('rolesError').classList.remove('hidden');
            return;
        }

        const formData = new FormData(event.target);
        
        try {
            const response = await fetch('{{ route('admin.users.store-quick') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Error al crear usuario');
            }

            // Mostrar modal de éxito
            document.getElementById('successName').textContent = data.data.name;
            document.getElementById('successEmail').textContent = data.data.email;
            document.getElementById('successPassword').textContent = data.data.password;
            
            const rolesDiv = document.getElementById('successRoles');
            rolesDiv.innerHTML = data.data.roles.split(',').map(role => `
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    ${role.trim()}
                </span>
            `).join('');

            successModal.classList.remove('hidden');

        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    function copyPassword() {
        const password = document.getElementById('successPassword').textContent;
        navigator.clipboard.writeText(password).then(() => {
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = '¡Copiado!';
            btn.classList.add('bg-green-100', 'text-green-700');
            setTimeout(() => {
                btn.textContent = originalText;
                btn.classList.remove('bg-green-100', 'text-green-700');
            }, 2000);
        });
    }

    function resetForm() {
        searchInput.value = '';
        usersList.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
                <p class="text-gray-500">Escribe para buscar usuarios...</p>
            </div>
        `;
        selectedUserInfo.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <p class="text-sm">Selecciona un usuario</p>
            </div>
        `;
        quickUserForm.classList.add('hidden');
        successModal.classList.add('hidden');
        searchInput.focus();
    }

    // Obtener token CSRF si no existe
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.head.appendChild(meta);
    }
</script>
@endsection
