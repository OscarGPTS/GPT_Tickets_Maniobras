@extends('layouts.app')

@section('title', 'Historial de Tickets')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Historial Completo de Tickets</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Todos los tickets que he atendido - {{ $tickets->total() }} tickets encontrados
                    </p>
                </div>
                <div class="flex gap-3">
                    <button onclick="document.getElementById('exportForm').submit()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-green-700">
                        <i class="fas fa-file-excel mr-2"></i>
                        Exportar Excel
                    </button>
                    <a href="{{ route('almacen.tickets.mine') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver a Tickets Activos
                    </a>
                </div>
            </div>
        </div>

        <!-- Formulario de exportación (oculto) -->
        <form id="exportForm" action="{{ route('almacen.tickets.export') }}" method="GET" class="hidden">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
            <input type="hidden" name="date_to" value="{{ request('date_to') }}">
            <input type="hidden" name="assigned_to" value="{{ Auth::id() }}">
        </form>

        <!-- Estadísticas del Usuario -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-blue-600 text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pendiente'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-spinner text-blue-600 text-lg"></i>
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
            <form method="GET" action="{{ route('almacen.tickets.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status" name="status" 
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_proceso" {{ request('status') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="finalizado" {{ request('status') == 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>
                        Filtrar
                    </button>
                    @if(request()->hasAny(['status', 'date_from', 'date_to']))
                        <a href="{{ route('almacen.tickets.history') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-400">
                            <i class="fas fa-times mr-1"></i> Limpiar
                        </a>
                    @endif
                </div>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calificación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- ID -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-semibold text-gray-900">{{ $ticket->formatted_code }}</p>
                                    </td>

                                    <!-- Título -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->title, 40) }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($ticket->description, 60) }}</div>
                                    </td>

                                    <!-- Solicitante -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $ticket->user->email }}</div>
                                    </td>

                                    <!-- Estado -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ticket->status === 'pendiente')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pendiente
                                            </span>
                                        @elseif($ticket->status === 'en_proceso')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-spinner mr-1"></i>
                                                En Proceso
                                            </span>
                                        @elseif($ticket->status === 'finalizado')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Finalizado
                                            </span>
                                        @elseif($ticket->status === 'cancelado')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Cancelado
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Fechas -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <div class="mb-1">
                                                <span class="text-xs text-gray-500">Creado:</span>
                                                <span class="font-medium">{{ $ticket->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            @if($ticket->assigned_at)
                                                <div class="mb-1">
                                                    <span class="text-xs text-gray-500">Asignado:</span>
                                                    <span class="font-medium">{{ $ticket->assigned_at->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                            @if($ticket->completed_at)
                                                <div>
                                                    <span class="text-xs text-gray-500">Completado:</span>
                                                    <span class="font-medium text-green-600">{{ $ticket->completed_at->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Calificación -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ticket->survey && $ticket->survey->completed_at)
                                            <div class="flex items-center">
                                                <span class="text-lg font-bold text-yellow-500">{{ $ticket->survey->rating }}</span>
                                                <i class="fas fa-star text-yellow-400 ml-1"></i>
                                            </div>
                                            @if($ticket->survey->comments)
                                                <p class="text-xs text-gray-500 mt-1">Con comentarios</p>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">Sin calificar</span>
                                        @endif
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                           class="inline-flex items-center px-3 py-1.5 
                                                  {{ $ticket->status === 'finalizado' ? 'bg-gray-100 text-gray-800 hover:bg-gray-200' : 'bg-blue-600 text-white hover:bg-blue-700' }}
                                                  text-xs font-medium rounded transition-colors">
                                            <i class="fas {{ $ticket->status === 'finalizado' ? 'fa-eye' : 'fa-cog' }} mr-1"></i>
                                            {{ $ticket->status === 'finalizado' ? 'Ver Detalles' : 'Trabajar' }}
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay tickets en el historial</h3>
                    <p class="text-gray-600 mb-4">
                        @if(request()->hasAny(['status', 'date_from', 'date_to']))
                            No se encontraron tickets con los filtros seleccionados.
                        @else
                            Aún no tienes tickets asignados en tu historial.
                        @endif
                    </p>
                    <div class="flex justify-center gap-3">
                        @if(request()->hasAny(['status', 'date_from', 'date_to']))
                            <a href="{{ route('almacen.tickets.history') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                <i class="fas fa-times mr-2"></i>
                                Limpiar Filtros
                            </a>
                        @endif
                        <a href="{{ route('almacen.tickets.mine') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver a Tickets Activos
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
