@extends('layouts.app')

@section('title', 'Panel Administrativo')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <span class="bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-full p-2 mr-3">
                        <i class="fas fa-user-shield"></i>
                    </span>
                    Panel Administrativo
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Gestiona usuarios, roles y supervisa el sistema completo
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Última actualización</p>
                <p class="text-lg font-semibold text-gray-900">{{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Estadísticas Principales con Gradientes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Usuarios -->
        <div class="bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium uppercase tracking-wide">Total Usuarios</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-purple-100 mt-1">Registrados</p>
                </div>
                <div class="bg-purple-500 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Tickets -->
        <div class="bg-gradient-to-br from-blue-400 to-cyan-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Total Tickets</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_tickets'] }}</p>
                    <p class="text-xs text-blue-100 mt-1">Solicitudes</p>
                </div>
                <div class="bg-blue-500 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-ticket-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Tickets Activos -->
        <div class="bg-gradient-to-br from-orange-400 to-red-500 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Tickets Activos</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['pending_tickets'] + $stats['in_progress_tickets'] }}</p>
                    <p class="text-xs text-orange-100 mt-1">Pendientes + En proceso</p>
                </div>
                <div class="bg-orange-500 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-hourglass-half text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Satisfacción -->
        <div class="bg-gradient-to-br from-green-400 to-emerald-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Satisfacción</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['average_rating'], 1) }}/5</p>
                    <p class="text-xs text-green-100 mt-1">{{ $stats['satisfaction_percentage'] }}% ≥4 estrellas</p>
                </div>
                <div class="bg-green-500 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-star text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Acciones Rápidas
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Gestionar Usuarios -->
            <a href="{{ route('admin.users.index') }}" class="group flex items-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all transform hover:scale-105">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-700 transition-colors">
                    <i class="fas fa-users text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Gestionar Usuarios</p>
                    <p class="text-xs text-gray-600">Ver y editar usuarios</p>
                </div>
            </a>

            <!-- Ver Estadísticas -->
            <a href="{{ route('admin.statistics') }}" class="group flex items-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all transform hover:scale-105">
                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-700 transition-colors">
                    <i class="fas fa-chart-bar text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Estadísticas</p>
                    <p class="text-xs text-gray-600">Reportes avanzados</p>
                </div>
            </a>

            <!-- Ver Tickets -->
            <a href="{{ route('tickets.index') }}" class="group flex items-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all transform hover:scale-105">
                <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-700 transition-colors">
                    <i class="fas fa-clipboard-list text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Ver Tickets</p>
                    <p class="text-xs text-gray-600">Todos los tickets</p>
                </div>
            </a>

            <!-- Ver Encuestas -->
            <a href="{{ route('surveys.index') }}" class="group flex items-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg hover:from-yellow-100 hover:to-yellow-200 transition-all transform hover:scale-105">
                <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center mr-3 group-hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-star text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Ver Encuestas</p>
                    <p class="text-xs text-gray-600">Estado de encuestas</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Gráficos y Estadísticas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Gráfico de Usuarios -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 text-center flex items-center justify-center">
                <i class="fas fa-users text-purple-600 mr-2"></i>
                Distribución de Usuarios
            </h3>
            <div class="relative h-64">
                <canvas id="usersChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between p-2 bg-red-50 rounded">
                    <span class="text-sm font-medium text-gray-700 flex items-center">
                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                        Administradores
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $usersByRole['admin'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                    <span class="text-sm font-medium text-gray-700 flex items-center">
                        <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                        Personal de Almacén
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $usersByRole['almacen'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                    <span class="text-sm font-medium text-gray-700 flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        Solicitantes
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $usersByRole['solicitante'] }}</span>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 text-center flex items-center justify-center">
                <i class="fas fa-tasks text-blue-600 mr-2"></i>
                Estado de Tickets
            </h3>
            <div class="relative h-64">
                <canvas id="ticketsChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                    <span class="text-sm font-medium text-gray-700 flex items-center">
                        <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                        Pendientes
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $stats['pending_tickets'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                    <span class="text-sm font-medium text-gray-700 flex items-center">
                        <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                        En Proceso
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $stats['in_progress_tickets'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                    <span class="text-sm font-medium text-gray-700 flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        Finalizados
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $stats['completed_tickets'] }}</span>
                </div>
            </div>
        </div>

        <!-- Gráfico de Encuestas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 text-center flex items-center justify-center">
                <i class="fas fa-clipboard-check text-green-600 mr-2"></i>
                Estado de Encuestas
            </h3>
            <div class="relative h-64">
                <canvas id="surveysChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                    <span class="text-sm font-medium text-gray-700 flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        Completadas
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $stats['completed_surveys'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                    <span class="text-sm font-medium text-gray-700 flex items-center">
                        <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                        Pendientes
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $stats['pending_surveys'] }}</span>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['average_rating'], 1) }}/5</p>
                        <p class="text-xs text-gray-500">Calificación Promedio</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Usuarios Recientes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                    Usuarios Recientes
                </h3>
                <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    Ver todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentUsers as $user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center flex-1">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-bold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                @php
                                    $userRole = $user->roles->first();
                                    $roleName = $userRole ? $userRole->name : 'solicitante';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                    {{ $roleName === 'admin' ? 'bg-red-100 text-red-800' : ($roleName === 'almacen' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($roleName) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No hay usuarios recientes</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tickets Recientes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <i class="fas fa-ticket-alt text-purple-600 mr-2"></i>
                    Tickets Recientes
                </h3>
                <a href="{{ route('tickets.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    Ver todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentTickets as $ticket)
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border-l-4 {{ $ticket->status === 'finalizado' ? 'border-green-400' : ($ticket->status === 'en_proceso' ? 'border-blue-400' : 'border-yellow-400') }}">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-bold text-gray-900">
                                    <span class="text-indigo-600">#{{ $ticket->id }}</span> - {{ Str::limit($ticket->title, 25) }}
                                </p>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full
                                    {{ $ticket->status === 'finalizado' ? 'bg-green-100 text-green-800' : ($ticket->status === 'en_proceso' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    <i class="fas {{ $ticket->status === 'finalizado' ? 'fa-check-circle' : ($ticket->status === 'en_proceso' ? 'fa-spinner' : 'fa-clock') }} mr-1"></i>
                                    {{ $ticket->status === 'en_proceso' ? 'En Proceso' : ucfirst($ticket->status) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $ticket->user->name }}
                                </span>
                                <span class="text-gray-500">{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                            @if($ticket->assignedTo)
                                <p class="text-xs text-blue-600 mt-1">
                                    <i class="fas fa-user-check mr-1"></i>
                                    Asignado a: {{ $ticket->assignedTo->name }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No hay tickets recientes</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Rendimiento por Miembro de Almacén -->
    @if(count($almacenStats) > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                    Rendimiento por Miembro de Almacén
                </h3>
                <p class="text-sm text-gray-600 mt-1">Estadísticas de satisfacción y calificaciones por empleado</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($almacenStats as $member)
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-lg font-bold">{{ strtoupper(substr($member['name'], 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-lg font-bold text-gray-900">{{ $member['name'] }}</h4>
                                        <p class="text-sm text-gray-500">{{ $member['email'] }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @php
                                        $satisfaction = $member['satisfaction_percentage'];
                                        $satisfactionColor = $satisfaction >= 80 ? 'text-green-600' : ($satisfaction >= 60 ? 'text-yellow-600' : 'text-red-600');
                                        $satisfactionBg = $satisfaction >= 80 ? 'bg-green-100' : ($satisfaction >= 60 ? 'bg-yellow-100' : 'bg-red-100');
                                    @endphp
                                    <div class="{{ $satisfactionBg }} rounded-lg px-3 py-2">
                                        <div class="text-2xl font-bold {{ $satisfactionColor }}">{{ $satisfaction }}%</div>
                                        <div class="text-xs text-gray-600">Satisfacción</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                                    <div class="text-xl font-bold text-gray-900">{{ $member['total_tickets'] }}</div>
                                    <div class="text-xs text-gray-500">Tickets Asignados</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                                    <div class="text-xl font-bold text-green-600">{{ $member['completed_tickets'] }}</div>
                                    <div class="text-xs text-gray-500">Completados</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                                    <div class="text-xl font-bold text-blue-600">{{ $member['total_surveys'] }}</div>
                                    <div class="text-xs text-gray-500">Encuestas</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                                    @php
                                        $rating = $member['average_rating'];
                                        $ratingColor = $rating >= 4 ? 'text-green-600' : ($rating >= 3 ? 'text-yellow-600' : 'text-red-600');
                                    @endphp
                                    <div class="text-xl font-bold {{ $ratingColor }}">
                                        <i class="fas fa-star text-yellow-400"></i>
                                        {{ $rating }}/5
                                    </div>
                                    <div class="text-xs text-gray-500">Calificación</div>
                                </div>
                            </div>
                            
                            <!-- Barra de progreso -->
                            <div class="mt-3">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">Nivel de Satisfacción</span>
                                    <span class="text-sm font-bold {{ $satisfactionColor }}">{{ $satisfaction }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    @php
                                        $progressColor = $satisfaction >= 80 ? 'bg-green-500' : ($satisfaction >= 60 ? 'bg-yellow-500' : 'bg-red-500');
                                    @endphp
                                    <div class="{{ $progressColor }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $satisfaction }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla de Todos los Tickets -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-list text-gray-600 mr-2"></i>
                Todos los Tickets
            </h3>
            <p class="text-sm text-gray-600 mt-1">Listado completo ordenado por más recientes</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Solicitante</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Asignado</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Calificación</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($allTickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-indigo-600">#{{ $ticket->id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ Str::limit($ticket->title, 30) }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($ticket->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ strtoupper(substr($ticket->user->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->user->name, 20) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->assignedTo)
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">{{ strtoupper(substr($ticket->assignedTo->name, 0, 1)) }}</span>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-900">{{ Str::limit($ticket->assignedTo->name, 15) }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sin asignar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'pendiente' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock', 'label' => 'Pendiente'],
                                        'en_proceso' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-spinner', 'label' => 'En Proceso'],
                                        'finalizado' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'label' => 'Finalizado'],
                                        'cancelado' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle', 'label' => 'Cancelado'],
                                    ];
                                    $config = $statusConfig[$ticket->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-question', 'label' => ucfirst($ticket->status)];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $config['bg'] }} {{ $config['text'] }}">
                                    <i class="fas {{ $config['icon'] }} mr-1"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->survey && $ticket->survey->completed_at && $ticket->survey->rating)
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-xs {{ $i <= $ticket->survey->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                        <span class="ml-1 text-xs font-bold text-gray-700">({{ $ticket->survey->rating }})</span>
                                    </div>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                        Por evaluar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="text-xs font-medium">{{ $ticket->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $ticket->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('tickets.show', $ticket) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                                    <i class="fas fa-eye mr-1"></i>
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-gray-400 text-5xl mb-3"></i>
                                <h3 class="text-sm font-medium text-gray-900">No hay tickets</h3>
                                <p class="text-sm text-gray-500 mt-1">No se han creado tickets aún.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($allTickets->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $allTickets->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Configuración global
Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
Chart.defaults.color = '#6B7280';

// Gráfico de Usuarios
const usersCtx = document.getElementById('usersChart').getContext('2d');
new Chart(usersCtx, {
    type: 'doughnut',
    data: {
        labels: ['Administradores', 'Personal Almacén', 'Solicitantes'],
        datasets: [{
            data: [{{ $usersByRole['admin'] }}, {{ $usersByRole['almacen'] }}, {{ $usersByRole['solicitante'] }}],
            backgroundColor: ['#EF4444', '#3B82F6', '#10B981'],
            borderWidth: 0,
            cutout: '65%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Gráfico de Tickets
const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
new Chart(ticketsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pendientes', 'En Proceso', 'Finalizados'],
        datasets: [{
            data: [{{ $stats['pending_tickets'] }}, {{ $stats['in_progress_tickets'] }}, {{ $stats['completed_tickets'] }}],
            backgroundColor: ['#F59E0B', '#3B82F6', '#10B981'],
            borderWidth: 0,
            cutout: '65%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Gráfico de Encuestas
const surveysCtx = document.getElementById('surveysChart').getContext('2d');
new Chart(surveysCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completadas', 'Pendientes'],
        datasets: [{
            data: [{{ $stats['completed_surveys'] }}, {{ $stats['pending_surveys'] }}],
            backgroundColor: ['#10B981', '#F59E0B'],
            borderWidth: 0,
            cutout: '65%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>

@endsection
