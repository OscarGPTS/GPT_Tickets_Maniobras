@extends('layouts.app')

@section('title', 'Estadísticas del Sistema')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Estadísticas del Sistema</h1>
                        <p class="text-gray-600 mt-2">Análisis detallado del rendimiento y uso del sistema</p>
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

        <!-- Resumen General -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Total Usuarios -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1.5H5a2 2 0 00-2 2v10a2 2 0 002 2h6.5"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Usuarios</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['users']['total'] }}</p>
                        <p class="text-xs text-green-600">+{{ $stats['users']['new_this_month'] }} este mes</p>
                    </div>
                </div>
            </div>

            <!-- Total Tickets -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Tickets</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['tickets']['total'] }}</p>
                        <p class="text-xs text-green-600">+{{ $stats['tickets']['this_month'] }} este mes</p>
                    </div>
                </div>
            </div>

            <!-- Tasa de Completación -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Tasa de Completación</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['tickets']['completion_rate'] }}%</p>
                        <p class="text-xs text-gray-600">Tickets completados</p>
                    </div>
                </div>
            </div>

            <!-- Promedio Encuestas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Calificación Promedio</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['surveys']['average_rating'], 1) }}/5</p>
                        <p class="text-xs text-gray-600">{{ $stats['surveys']['completed'] }} encuestas</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Gráficos y Detalles -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Panel Izquierdo - Detalles por Categoría -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Usuarios por Rol -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Usuarios por Rol</h3>
                    
                    <div class="space-y-3">
                        <!-- Administradores -->
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Administradores</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">{{ $stats['users']['by_role']['admin'] }}</span>
                                <p class="text-xs text-gray-500">
                                    {{ $stats['users']['total'] > 0 ? round(($stats['users']['by_role']['admin'] / $stats['users']['total']) * 100, 1) : 0 }}%
                                </p>
                            </div>
                        </div>
                        
                        <!-- Personal de Almacén -->
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Personal de Almacén</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">{{ $stats['users']['by_role']['almacen'] }}</span>
                                <p class="text-xs text-gray-500">
                                    {{ $stats['users']['total'] > 0 ? round(($stats['users']['by_role']['almacen'] / $stats['users']['total']) * 100, 1) : 0 }}%
                                </p>
                            </div>
                        </div>
                        
                        <!-- Solicitantes -->
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Solicitantes</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">{{ $stats['users']['by_role']['solicitante'] }}</span>
                                <p class="text-xs text-gray-500">
                                    {{ $stats['users']['total'] > 0 ? round(($stats['users']['by_role']['solicitante'] / $stats['users']['total']) * 100, 1) : 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado de Tickets -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado de Tickets</h3>
                    
                    <div class="space-y-3">
                        <!-- Pendientes -->
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Pendientes</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">{{ $stats['tickets']['by_status']['pendiente'] }}</span>
                                <p class="text-xs text-gray-500">
                                    {{ $stats['tickets']['total'] > 0 ? round(($stats['tickets']['by_status']['pendiente'] / $stats['tickets']['total']) * 100, 1) : 0 }}%
                                </p>
                            </div>
                        </div>
                        
                        <!-- En Progreso -->
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">En Progreso</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">{{ $stats['tickets']['by_status']['en_progreso'] }}</span>
                                <p class="text-xs text-gray-500">
                                    {{ $stats['tickets']['total'] > 0 ? round(($stats['tickets']['by_status']['en_progreso'] / $stats['tickets']['total']) * 100, 1) : 0 }}%
                                </p>
                            </div>
                        </div>
                        
                        <!-- Completados -->
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Completados</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">{{ $stats['tickets']['by_status']['completado'] }}</span>
                                <p class="text-xs text-gray-500">
                                    {{ $stats['tickets']['total'] > 0 ? round(($stats['tickets']['by_status']['completado'] / $stats['tickets']['total']) * 100, 1) : 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Panel Derecho - Gráfico de Tendencias -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Tendencias Mensuales (Últimos 6 meses)</h3>
                    </div>
                    
                    <!-- Gráfico Simple con CSS -->
                    <div class="space-y-6">
                        @foreach($monthlyData as $month)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $month['month'] }}</h4>
                                </div>
                                
                                <!-- Usuarios -->
                                <div class="mb-2">
                                    <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                        <span>Usuarios nuevos</span>
                                        <span>{{ $month['users'] }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stats['users']['total'] > 0 ? ($month['users'] / max($monthlyData->pluck('users')->toArray()) * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                                
                                <!-- Tickets -->
                                <div class="mb-2">
                                    <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                        <span>Tickets creados</span>
                                        <span>{{ $month['tickets'] }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $stats['tickets']['total'] > 0 ? ($month['tickets'] / max($monthlyData->pluck('tickets')->toArray()) * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                                
                                <!-- Encuestas -->
                                <div>
                                    <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                        <span>Encuestas creadas</span>
                                        <span>{{ $month['surveys'] }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $stats['surveys']['total'] > 0 ? ($month['surveys'] / max($monthlyData->pluck('surveys')->toArray()) * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Leyenda -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center space-x-6 text-sm">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-gray-600">Usuarios</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-gray-600">Tickets</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                                <span class="text-gray-600">Encuestas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Estadísticas de Encuestas -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Estadísticas de Encuestas de Satisfacción</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Encuestas Totales -->
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['surveys']['total'] }}</div>
                        <div class="text-sm text-gray-600">Total de Encuestas</div>
                    </div>
                    
                    <!-- Encuestas Completadas -->
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-3xl font-bold text-green-600">{{ $stats['surveys']['completed'] }}</div>
                        <div class="text-sm text-gray-600">Completadas</div>
                        <div class="text-xs text-green-600 mt-1">{{ $stats['surveys']['completion_rate'] }}% de finalización</div>
                    </div>
                    
                    <!-- Encuestas Pendientes -->
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-3xl font-bold text-yellow-600">{{ $stats['surveys']['pending'] }}</div>
                        <div class="text-sm text-gray-600">Pendientes</div>
                        <div class="text-xs text-yellow-600 mt-1">
                            {{ $stats['surveys']['total'] > 0 ? round(($stats['surveys']['pending'] / $stats['surveys']['total']) * 100, 1) : 0 }}% sin completar
                        </div>
                    </div>

                </div>

                <!-- Calificación Promedio -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <h4 class="text-md font-semibold text-gray-900 mb-2">Calificación Promedio de Satisfacción</h4>
                        <div class="flex items-center justify-center space-x-1 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= round($stats['surveys']['average_rating']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['surveys']['average_rating'], 2) }} / 5.00</p>
                        <p class="text-sm text-gray-600">Basado en {{ $stats['surveys']['completed'] }} respuestas</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection