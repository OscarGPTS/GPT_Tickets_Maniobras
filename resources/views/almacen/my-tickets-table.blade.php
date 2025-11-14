@extends('layouts.app')

@section('title', 'Mis Tickets - Historial')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Mis Tickets</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Historial completo de tickets asignados a mí
                    </p>
                </div>
                <a href="{{ route('almacen.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Estadísticas del Usuario -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-blue-600 text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Asignados</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-spinner text-yellow-600 text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">En Proceso</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['en_proceso'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Completados</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['completados'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-purple-600 text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Calificación</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['promedio_calificacion'] }}/5</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('almacen.tickets.mine') }}" class="flex items-center space-x-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por estado:</label>
                    <select id="status" name="status" 
                            onchange="this.form.submit()"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                        <option value="">Todos los estados</option>
                        <option value="en_proceso" {{ request('status') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="finalizado" {{ request('status') == 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                    </select>
                </div>
                @if(request('status'))
                    <div class="mt-6">
                        <a href="{{ route('almacen.tickets.mine') }}" 
                           class="text-sm text-blue-600 hover:text-blue-800">
                            <i class="fas fa-times-circle mr-1"></i> Limpiar filtros
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Tabla de Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">
                    Historial de Tickets
                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $tickets->total() }} total)</span>
                </h2>
            </div>

            @if($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID / Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiempo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calificación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- ID / Título -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold 
                                                {{ $ticket->status === 'finalizado' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                #{{ $ticket->id }}
                                            </span>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->title, 40) }}</div>
                                                <div class="text-xs text-gray-500">{{ Str::limit($ticket->description, 60) }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Solicitante -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $ticket->user->email }}</div>
                                    </td>

                                    <!-- Estado -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ticket->status === 'en_proceso')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-spinner mr-1"></i> En Progreso
                                            </span>
                                        @elseif($ticket->status === 'finalizado')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Finalizado
                                            </span>
                                        @elseif($ticket->status === 'cancelado')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i> Cancelado
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Fechas -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <div class="mb-1">
                                                <span class="text-xs text-gray-500">Solicitud:</span>
                                                <span class="font-medium">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="mb-1">
                                                <span class="text-xs text-gray-500">Asignado:</span>
                                                <span class="font-medium">{{ $ticket->assigned_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            @if($ticket->completed_at)
                                                <div>
                                                    <span class="text-xs text-gray-500">Completado:</span>
                                                    <span class="font-medium text-green-600">{{ $ticket->completed_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Tiempo de Resolución -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ticket->completed_at && $ticket->assigned_at)
                                            @php
                                                $diff = $ticket->assigned_at->diff($ticket->completed_at);
                                                $hours = ($diff->days * 24) + $diff->h;
                                                $minutes = $diff->i;
                                            @endphp
                                            <div class="text-sm text-gray-900">
                                                <span class="font-semibold">{{ $hours }}h {{ $minutes }}m</span>
                                            </div>
                                            <div class="text-xs text-gray-500">Tiempo total</div>
                                        @else
                                            <div class="text-sm text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>En curso
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Calificación -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ticket->survey && $ticket->survey->completed_at && $ticket->survey->rating)
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $ticket->survey->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <div class="text-xs text-gray-600 mt-1">{{ $ticket->survey->rating }}/5</div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                Sin calificar
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                           class="inline-flex items-center px-3 py-1.5 
                                                  {{ ($ticket->status === 'finalizado' || $ticket->status === 'cancelado') ? 'bg-gray-100 text-gray-800 hover:bg-gray-200' : 'bg-blue-600 text-white hover:bg-blue-700' }}
                                                  text-xs font-medium rounded transition-colors">
                                            <i class="fas {{ ($ticket->status === 'finalizado' || $ticket->status === 'cancelado') ? 'fa-eye' : 'fa-cog' }} mr-1"></i>
                                            {{ ($ticket->status === 'finalizado' || $ticket->status === 'cancelado') ? 'Ver' : 'Trabajar' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($tickets->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $tickets->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sin tickets asignados</h3>
                    <p class="text-gray-600 mb-4">
                        @if(request('status'))
                            No hay tickets con el filtro seleccionado.
                        @else
                            Aún no tienes tickets asignados en tu historial.
                        @endif
                    </p>
                    <a href="{{ route('almacen.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
