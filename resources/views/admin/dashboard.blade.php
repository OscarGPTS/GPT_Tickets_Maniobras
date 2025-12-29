@extends('layouts.app')

@section('title', 'Panel Administrativo')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Panel Administrativo</h1>
                        <p class="text-gray-600 mt-2">Gestiona usuarios, roles y supervisa el sistema</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Última actualización</p>
                        <p class="text-lg font-semibold text-gray-900">{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN PRIORITARIA: Solicitudes Pendientes de Aprobar -->
        <div class="mb-8 mt-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Solicitudes Pendientes de Aprobar</h2>
                            <p class="text-sm text-gray-600 mt-1">Estos son los tickets que requieren tu aprobación e asignación inmediata</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-4xl font-bold text-orange-600">{{ count($pendingTickets) }}</div>
                        <p class="text-sm text-gray-600">Por aprobar</p>
                    </div>
                </div>

                @if(count($pendingTickets) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-orange-200">
                            <thead class="bg-orange-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-orange-900 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-orange-900 uppercase tracking-wider">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-orange-900 uppercase tracking-wider">Solicitante</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-orange-900 uppercase tracking-wider">Descripción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-orange-900 uppercase tracking-wider">Fecha Solicitud</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-orange-900 uppercase tracking-wider">Imágenes</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-orange-900 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-orange-100">
                                @forelse($pendingTickets as $ticket)
                                    <tr class="hover:bg-orange-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                                #{{ $ticket->id }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ Str::limit($ticket->title, 40) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                                    <span class="text-white text-xs font-bold">{{ substr($ticket->user->name, 0, 1) }}</span>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $ticket->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-600">{{ Str::limit($ticket->description, 50) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ $ticket->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs">{{ $ticket->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($ticket->solicitudImages && count($ticket->solicitudImages) > 0)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M5.5 13a3 3 0 01-.369-5.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
                                                    </svg>
                                                    {{ count($ticket->solicitudImages) }} imagen(es)
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-600">
                                                    Sin imágenes
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Ver
                                                </a>
                                                <a href="{{ route('almacen.tickets.show', $ticket) }}#asignar" 
                                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    Asignar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12">
                                            <div class="text-center">
                                                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">¡Excelente!</h3>
                                                <p class="mt-1 text-sm text-gray-500">No hay solicitudes pendientes de aprobar. Todas han sido procesadas.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center items-center py-8">
                        <div class="flex justify-center items-center py-8">
                            <svg width="80px" height="80px" viewBox="0 0 1024 1024" class="icon"  version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M512 512m-448 0a448 448 0 1 0 896 0 448 448 0 1 0-896 0Z" fill="#4CAF50" /><path d="M738.133333 311.466667L448 601.6l-119.466667-119.466667-59.733333 59.733334 179.2 179.2 349.866667-349.866667z" fill="#CCFF90" /></svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">¡Perfecto!</h3>
                        <p class="mt-2 text-gray-600">No hay solicitudes pendientes de aprobar. Todas han sido procesadas.</p>
                    </div>
                @endif
            </div>
        </div>

         <!-- Acciones Rápidas -->
        <div class="mt-8 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Gestionar Usuarios -->
                    <a href="{{ route('admin.users.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1.5H5a2 2 0 00-2 2v10a2 2 0 002 2h6.5"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Gestionar Usuarios</p>
                            <p class="text-xs text-gray-600">Ver y editar usuarios</p>
                        </div>
                    </a>

                    <!-- Ver Estadísticas -->
                    <a href="{{ route('admin.statistics') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Estadísticas</p>
                            <p class="text-xs text-gray-600">Reportes avanzados</p>
                        </div>
                    </a>

                    <!-- Ver Tickets -->
                    <a href="{{ route('admin.tickets.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Ver Tickets</p>
                            <p class="text-xs text-gray-600">Todos los tickets</p>
                        </div>
                    </a>

                    <!-- Ver Encuestas -->
                    <a href="{{ route('admin.tickets.index', ['status' => 'completado']) }}" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        <div class="w-8 h-8 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Tickets Completados</p>
                            <p class="text-xs text-gray-600">Ver encuestas y calificaciones</p>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        <!-- ESTADÍSTICAS DEL SISTEMA (Desglosadas) -->
        <div class="mt-12 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Estadísticas del Sistema</h2>

                <!-- Estadísticas Principales con Chart.js -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Gráfico Total Usuarios -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Distribución de Usuarios</h3>
                <div class="relative h-64">
                    <canvas id="usersChart" width="200" height="200"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                    <p class="text-sm text-gray-500">Total de Usuarios</p>
                </div>
            </div>

            <!-- Gráfico Total Tickets por Estado -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Estado de Tickets</h3>
                <div class="relative h-64">
                    <canvas id="ticketsChart" width="200" height="200"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_tickets'] }}</p>
                    <p class="text-sm text-gray-500">Total de Tickets</p>
                </div>
            </div>

            <!-- Gráfico de Encuestas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Estado de Encuestas</h3>
                <div class="relative h-64">
                    <canvas id="surveysChart" width="200" height="200"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_surveys'] }}</p>
                    <p class="text-sm text-gray-500">Total de Encuestas</p>
                </div>
                </div>
            </div>
        </div>

        <!-- Grid de Contenido Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Panel Izquierdo - Usuarios por Rol -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Usuarios por Rol</h3>
                    
                    <div class="space-y-4">
                        <!-- Administradores -->
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Administradores</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ $usersByRole['admin'] }}</span>
                        </div>
                        
                        <!-- Personal de Almacén -->
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Personal de Almacén</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ $usersByRole['almacen'] }}</span>
                        </div>
                        
                        <!-- Solicitantes -->
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Solicitantes</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ $usersByRole['solicitante'] }}</span>
                        </div>
                    </div>

                    <!-- Encuestas -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Estado de Encuestas</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-medium">{{ $stats['total_surveys'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Completadas:</span>
                                <span class="font-medium text-green-600">{{ $stats['completed_surveys'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pendientes:</span>
                                <span class="font-medium text-yellow-600">{{ $stats['pending_surveys'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Calificación promedio:</span>
                                <span class="font-medium text-blue-600">{{ number_format($stats['average_rating'], 1) }}/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Derecho - Actividad Reciente -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    
                    <!-- Usuarios Recientes -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Usuarios Recientes</h3>
                            <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Ver todos →
                            </a>
                        </div>
                        
                        <div class="space-y-3">
                            @forelse($recentUsers as $user)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-bold">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $userRole = $user->roles->first();
                                            $roleName = $userRole ? $userRole->name : 'solicitante';
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            @if($roleName === 'admin') bg-red-100 text-red-800
                                            @elseif($roleName === 'almacen') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($roleName) }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No hay usuarios recientes</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tickets Recientes -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Tickets Recientes</h3>
                            <a href="{{ route('almacen.dashboard') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Ver todos →
                            </a>
                        </div>
                        
                        <div class="space-y-3">
                            @forelse($recentTickets as $ticket)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-900">#{{ $ticket->id }} - {{ Str::limit($ticket->title, 25) }}</p>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            @if($ticket->status === 'completado') bg-green-100 text-green-800
                                            @elseif($ticket->status === 'en_proceso') bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>Por: {{ $ticket->user->name }}</span>
                                        <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($ticket->assignedTo)
                                        <p class="text-xs text-blue-600 mt-1">Asignado a: {{ $ticket->assignedTo->name }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No hay tickets recientes</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

        </div>
        
         <!-- Porcentaje de Satisfacción General -->
        <div class="mb-8 mt-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Satisfacción General del Servicio</h3>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-green-600">{{ $stats['satisfaction_percentage'] }}%</p>
                        <p class="text-sm text-gray-500">Calificaciones ≥ 4 estrellas</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_surveys'] }}</p>
                        <p class="text-sm text-gray-600">Total Encuestas</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $stats['completed_surveys'] }}</p>
                        <p class="text-sm text-gray-600">Completadas</p>
                    </div>
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['average_rating'] }}/5</p>
                        <p class="text-sm text-gray-600">Calificación Promedio</p>
                    </div>
                </div>
            </div>
        </div>

       

        <!-- Estadísticas por Miembro de Almacén -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Rendimiento por Miembro de Almacén</h3>
                    <p class="text-sm text-gray-600 mt-1">Estadísticas de satisfacción y calificaciones por empleado</p>
                </div>
                
                <div class="p-6">
                    @if(count($almacenStats) > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($almacenStats as $member)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">{{ substr($member['name'], 0, 1) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $member['name'] }}</h4>
                                                <p class="text-sm text-gray-500">{{ $member['email'] }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @php
                                                $satisfaction = $member['satisfaction_percentage'];
                                                $satisfactionColor = $satisfaction >= 80 ? 'text-green-600' : ($satisfaction >= 60 ? 'text-yellow-600' : 'text-red-600');
                                            @endphp
                                            <div class="text-2xl font-bold {{ $satisfactionColor }}">{{ $satisfaction }}%</div>
                                            <div class="text-xs text-gray-500">Satisfacción</div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center p-3 bg-white rounded-lg">
                                            <div class="text-lg font-bold text-gray-900">{{ $member['total_tickets'] }}</div>
                                            <div class="text-xs text-gray-500">Tickets Asignados</div>
                                        </div>
                                        <div class="text-center p-3 bg-white rounded-lg">
                                            <div class="text-lg font-bold text-gray-900">{{ $member['completed_tickets'] }}</div>
                                            <div class="text-xs text-gray-500">Tickets Completados</div>
                                        </div>
                                        <div class="text-center p-3 bg-white rounded-lg">
                                            <div class="text-lg font-bold text-gray-900">{{ $member['total_surveys'] }}</div>
                                            <div class="text-xs text-gray-500">Encuestas Recibidas</div>
                                        </div>
                                        <div class="text-center p-3 bg-white rounded-lg">
                                            @php
                                                $rating = $member['average_rating'];
                                                $ratingColor = $rating >= 4 ? 'text-green-600' : ($rating >= 3 ? 'text-yellow-600' : 'text-red-600');
                                            @endphp
                                            <div class="text-lg font-bold {{ $ratingColor }}">{{ $rating }}/5</div>
                                            <div class="text-xs text-gray-500">Calificación Promedio</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Barra de progreso para satisfacción -->
                                    <div class="mt-4">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm font-medium text-gray-700">Nivel de Satisfacción</span>
                                            <span class="text-sm text-gray-500">{{ $satisfaction }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            @php
                                                $progressColor = $satisfaction >= 80 ? 'bg-green-500' : ($satisfaction >= 60 ? 'bg-yellow-500' : 'bg-red-500');
                                            @endphp
                                            <div class="{{ $progressColor }} h-2 rounded-full" style="width: {{ $satisfaction }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay miembros de almacén</h3>
                            <p class="mt-1 text-sm text-gray-500">No se han encontrado usuarios con rol de almacén.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabla de Todos los Tickets -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Todos los Tickets</h3>
                    <p class="text-sm text-gray-600 mt-1">Tickets ordenados del más reciente al más antiguo</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado a</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calificación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($allTickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $ticket->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->title, 30) }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($ticket->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">{{ substr($ticket->user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $ticket->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($ticket->assignedTo)
                                            <div class="flex items-center">
                                                <div class="w-6 h-6 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center">
                                                    <span class="text-white text-xs font-bold">{{ substr($ticket->assignedTo->name, 0, 1) }}</span>
                                                </div>
                                                <span class="ml-2 text-sm text-gray-900">{{ $ticket->assignedTo->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                'en_proceso' => 'bg-blue-100 text-blue-800',
                                                'finalizado' => 'bg-green-100 text-green-800'
                                            ];
                                            $statusLabels = [
                                                'pendiente' => 'Pendiente',
                                                'en_proceso' => 'En Proceso',
                                                'finalizado' => 'Finalizado'
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ticket->survey && $ticket->survey->completed_at && $ticket->survey->rating)
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $ticket->survey->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                                <span class="ml-2 text-sm text-gray-600">({{ $ticket->survey->rating }}/5)</span>
                                            </div>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                                Por evaluar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $ticket->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs">{{ $ticket->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay tickets</h3>
                                        <p class="mt-1 text-sm text-gray-500">No se han creado tickets aún.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                @if($allTickets->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $allTickets->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Configuración global de Chart.js
Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
Chart.defaults.color = '#6B7280';

// Gráfico de Usuarios por Rol
const usersCtx = document.getElementById('usersChart').getContext('2d');
new Chart(usersCtx, {
    type: 'doughnut',
    data: {
        labels: ['Administradores', 'Personal Almacén', 'Solicitantes'],
        datasets: [{
            data: [{{ $usersByRole['admin'] }}, {{ $usersByRole['almacen'] }}, {{ $usersByRole['solicitante'] }}],
            backgroundColor: [
                '#EF4444', // Rojo para admin
                '#3B82F6', // Azul para almacén
                '#10B981'  // Verde para solicitantes
            ],
            borderWidth: 0,
            cutout: '60%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
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

// Gráfico de Tickets por Estado
const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
new Chart(ticketsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pendientes', 'En Progreso', 'Finalizados'],
        datasets: [{
            data: [{{ $stats['pending_tickets'] }}, {{ $stats['in_progress_tickets'] }}, {{ $stats['completed_tickets'] }}],
            backgroundColor: [
                '#F59E0B', // Amarillo para pendientes
                '#3B82F6', // Azul para en progreso
                '#10B981'  // Verde para finalizados
            ],
            borderWidth: 0,
            cutout: '60%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
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
            backgroundColor: [
                '#10B981', // Verde para completadas
                '#F59E0B'  // Amarillo para pendientes
            ],
            borderWidth: 0,
            cutout: '60%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
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