@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Gestión de Usuarios</h1>
                        <p class="text-gray-600 mt-2">Administra usuarios y asigna roles en el sistema</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
                    
                    <!-- Búsqueda -->
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar usuarios</label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}" 
                               placeholder="Buscar por nombre o email..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Filtro por Rol -->
                    <div class="w-full md:w-48">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por rol</label>
                        <select name="role" 
                                id="role"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Buscar
                        </button>
                        @if(request()->hasAny(['search', 'role']))
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Mensajes de Estado -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        @foreach($errors->all() as $error)
                            <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabla de Usuarios -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Header de la tabla -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Lista de Usuarios ({{ $users->total() }})
                    </h3>
                    <div class="text-sm text-gray-500">
                        Mostrando {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} de {{ $users->total() }}
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuario
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Roles Actuales
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha de Registro
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Gestionar Roles
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50" id="user-{{ $user->id }}">
                                    <!-- Usuario -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Roles Actuales -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($user->roles as $role)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($role->name === 'admin') bg-red-100 text-red-800
                                                    @elseif($role->name === 'almacen') bg-blue-100 text-blue-800
                                                    @else bg-green-100 text-green-800 @endif">
                                                    {{ ucfirst($role->name) }}
                                                </span>
                                            @empty
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Sin roles
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <!-- Fecha de Registro -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $user->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs">{{ $user->created_at->diffForHumans() }}</div>
                                    </td>

                                    <!-- Asignar Roles -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form class="assign-roles-form" data-user-id="{{ $user->id }}">
                                            @csrf
                                            <div class="flex flex-col space-y-2">
                                                <div class="text-xs text-gray-600 mb-1">Seleccionar roles:</div>
                                                @foreach($roles as $role)
                                                    <label class="flex items-center">
                                                        <input type="checkbox" 
                                                               name="roles[]" 
                                                               value="{{ $role }}" 
                                                               {{ $user->hasRole($role) ? 'checked' : '' }}
                                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        <span class="ml-2 text-xs">{{ ucfirst($role) }}</span>
                                                    </label>
                                                @endforeach
                                                <button type="submit" class="mt-2 px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    Actualizar Roles
                                                </button>
                                            </div>
                                        </form>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Editar -->
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="text-blue-600 hover:text-blue-900 text-xs bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded">
                                                Editar
                                            </a>
                                            
                                            <!-- Eliminar (solo si no es el usuario actual y no es el único admin) -->
                                            @if($user->id !== auth()->id() && !($user->hasRole('admin') && App\Models\User::role('admin')->count() <= 1))
                                                <form method="POST" action="{{ route('admin.users.delete', $user) }}" 
                                                      class="inline delete-user-form" 
                                                      data-user-name="{{ $user->name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900 text-xs bg-red-50 hover:bg-red-100 px-2 py-1 rounded">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Estado vacío -->
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1.5H5a2 2 0 00-2 2v10a2 2 0 002 2h6.5"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron usuarios</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'role']))
                            Intenta ajustar los filtros de búsqueda.
                        @else
                            No hay usuarios registrados en el sistema.
                        @endif
                    </p>
                </div>
            @endif

            <!-- Paginación -->
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif

        </div>

    </div>
</div>

<!-- Scripts -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Manejar asignación de roles con AJAX
    document.querySelectorAll('.assign-roles-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const userId = this.dataset.userId;
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Obtener roles seleccionados
            const selectedRoles = [];
            this.querySelectorAll('input[name="roles[]"]:checked').forEach(checkbox => {
                selectedRoles.push(checkbox.value);
            });
            
            // Validar que al menos un rol esté seleccionado
            if (selectedRoles.length === 0) {
                showNotification('Debes seleccionar al menos un rol', 'error');
                return;
            }
            
            // Deshabilitar botón
            submitBtn.disabled = true;
            submitBtn.textContent = 'Actualizando...';
            
            // Limpiar formData y agregar roles seleccionados
            formData.delete('roles[]');
            selectedRoles.forEach(role => {
                formData.append('roles[]', role);
            });
            
            fetch(`/admin/users/${userId}/assign-role`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar los roles actuales en la tabla
                    const userRow = document.getElementById(`user-${userId}`);
                    const rolesCell = userRow.querySelector('td:nth-child(2)');
                    
                    // Crear HTML para los nuevos roles
                    let rolesHtml = '<div class="flex flex-wrap gap-1">';
                    if (data.user.roles && data.user.roles.length > 0) {
                        data.user.roles.split(', ').forEach(role => {
                            let className = 'bg-green-100 text-green-800';
                            if (role === 'admin') className = 'bg-red-100 text-red-800';
                            else if (role === 'almacen') className = 'bg-blue-100 text-blue-800';
                            
                            rolesHtml += `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${className}">
                                ${role.charAt(0).toUpperCase() + role.slice(1)}
                            </span>`;
                        });
                    } else {
                        rolesHtml += '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Sin roles</span>';
                    }
                    rolesHtml += '</div>';
                    
                    rolesCell.innerHTML = rolesHtml;
                    
                    // Mostrar mensaje de éxito
                    showNotification(data.message, 'success');
                } else {
                    showNotification('Error al asignar los roles', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al asignar los roles', 'error');
            })
            .finally(() => {
                // Rehabilitar botón
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    });
    
    // Manejar confirmación de eliminación
    document.querySelectorAll('.delete-user-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const userName = this.dataset.userName;
            
            if (confirm(`¿Estás seguro de que quieres eliminar al usuario "${userName}"? Esta acción no se puede deshacer.`)) {
                this.submit();
            }
        });
    });
    
});

// Función para mostrar notificaciones
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full px-4 py-3 rounded-md shadow-lg transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' 
                    ? '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                    : '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                }
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Animar salida y eliminar
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
</script>
@endpush
@endsection