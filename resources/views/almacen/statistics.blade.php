@extends('layouts.app')

@section('title', 'Estadísticas - Almacén')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Estadísticas del Almacén</h1>
                        <p class="text-gray-600 mt-2">Rendimiento y métricas de gestión de tickets</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('almacen.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">En Progreso</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_in_progress'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Completados Hoy</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_completed_today'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Calificación Promedio</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}/5</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Estadísticas Temporales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Esta Semana</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Completados</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_completed_this_week'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">En progreso</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_in_progress'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Pendientes</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_pending'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Este Mes</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Completados</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_completed_this_month'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Promedio por día</span>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($stats['total_completed_this_month'] / now()->day, 1) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Calificación promedio</span>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}/5</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mi Rendimiento</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Completados hoy</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['my_completed_today'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total completados</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['my_total_completed'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Porcentaje del total</span>
                        @php
                            $totalCompleted = \App\Models\Ticket::where('status', 'completado')->count();
                            $percentage = $totalCompleted > 0 ? ($stats['my_total_completed'] / $totalCompleted) * 100 : 0;
                        @endphp
                        <span class="text-sm font-medium text-gray-900">{{ number_format($percentage, 1) }}%</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Gráfico de Rendimiento (Simulado) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rendimiento Semanal</h3>
                <div class="space-y-4">
                    @php
                        $days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                        $values = [3, 5, 2, 8, 6, 1, 0]; // Valores simulados
                        $maxValue = max($values);
                    @endphp
                    @foreach($days as $index => $day)
                        <div class="flex items-center">
                            <div class="w-12 text-sm text-gray-600">{{ $day }}</div>
                            <div class="flex-1 mx-4">
                                <div class="bg-gray-200 rounded-full h-4 relative">
                                    <div class="bg-blue-500 h-4 rounded-full transition-all duration-500" 
                                         style="width: {{ $maxValue > 0 ? ($values[$index] / $maxValue) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div class="w-8 text-sm text-gray-900 font-medium">{{ $values[$index] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución por Estado</h3>
                <div class="space-y-4">
                    @php
                        $totalTickets = $stats['total_pending'] + $stats['total_in_progress'] + $stats['total_completed_today'];
                        $statuses = [
                            ['name' => 'Completados', 'count' => $stats['total_completed_today'], 'color' => 'bg-green-500'],
                            ['name' => 'En Progreso', 'count' => $stats['total_in_progress'], 'color' => 'bg-blue-500'],
                            ['name' => 'Pendientes', 'count' => $stats['total_pending'], 'color' => 'bg-yellow-500'],
                        ];
                    @endphp
                    @foreach($statuses as $status)
                        <div class="flex items-center">
                            <div class="w-20 text-sm text-gray-600">{{ $status['name'] }}</div>
                            <div class="flex-1 mx-4">
                                <div class="bg-gray-200 rounded-full h-4 relative">
                                    <div class="{{ $status['color'] }} h-4 rounded-full transition-all duration-500" 
                                         style="width: {{ $totalTickets > 0 ? ($status['count'] / $totalTickets) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div class="w-12 text-sm text-gray-900 font-medium">{{ $status['count'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Tickets Completados Recientemente -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Tickets Completados Recientemente</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado a</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calificación</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentCompletedTickets as $ticket)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">#{{ $ticket->id }} - {{ Str::limit($ticket->title, 40) }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($ticket->description, 60) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $ticket->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $ticket->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $ticket->assignedTo->name ?? 'No asignado' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $ticket->completed_at->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $ticket->completed_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->survey && $ticket->survey->completed_at)
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $ticket->survey->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-600">({{ $ticket->survey->rating }})</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Sin tickets completados</h3>
                                    <p class="mt-1 text-sm text-gray-500">No hay tickets completados recientemente</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
