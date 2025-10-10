@extends('layouts.app')

@section('title', 'Mis Tickets')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mis Tickets</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Gestiona tus solicitudes de movimiento de carga
                </p>
            </div>
            @if(auth()->user()->canCreateTicket())
                <a href="{{ route('tickets.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    Nueva Solicitud
                </a>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle h-5 w-5 text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-800">
                                Tienes encuestas pendientes. Completa tus encuestas para crear nuevos tickets.
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('tickets.index', ['filter' => 'survey_pending']) }}" 
                                   class="text-sm font-medium text-yellow-800 underline hover:text-yellow-900">
                                    Ver tickets con encuestas pendientes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Filtros -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <label for="status-filter" class="text-sm font-medium text-gray-700">Estado:</label>
                        <select id="status-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label for="date-filter" class="text-sm font-medium text-gray-700">Fecha:</label>
                        <select id="date-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas</option>
                            <option value="today">Hoy</option>
                            <option value="week">Esta semana</option>
                            <option value="month">Este mes</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Tickets -->
        <div class="bg-white shadow rounded-lg">
            @if($tickets->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($tickets as $ticket)
                        <div class="p-6 hover:bg-gray-50 transition duration-150 ease-in-out">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-lg font-medium text-gray-900 truncate">
                                            {{ $ticket->title }}
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getStatusBadgeClass() }}">
                                            {{ $ticket->getStatusText() }}
                                        </span>
                                    </div>
                                    <div class="mt-1 flex items-center text-sm text-gray-500 space-x-4">
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        @if($ticket->assigned_to)
                                            <span class="flex items-center">
                                                <i class="fas fa-user mr-1"></i>
                                                Asignado a: {{ $ticket->assignedTo->name }}
                                            </span>
                                        @endif
                                        @if($ticket->completed_at)
                                            <span class="flex items-center">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Completado: {{ $ticket->completed_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                                        {{ Str::limit($ticket->description, 150) }}
                                    </p>
                                    
                                    <!-- Mostrar imágenes si las hay -->
                                    @if($ticket->images->count() > 0)
                                        <div class="mt-3 flex items-center text-sm text-gray-500">
                                            <i class="fas fa-images mr-1"></i>
                                            {{ $ticket->images->count() }} imagen(es) adjunta(s)
                                        </div>
                                    @endif

                                    <!-- Estado de encuesta -->
                                    @if($ticket->status === 'finalizado')
                                        @if($ticket->survey && $ticket->survey->completed_at)
                                            <div class="mt-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-star mr-1"></i>
                                                Encuesta completada ({{ $ticket->survey->rating }}/5)
                                            </div>
                                        @else
                                            <div class="mt-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Encuesta pendiente
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="flex-shrink-0 flex items-center space-x-2">
                                    <a href="{{ route('tickets.show', $ticket) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-eye mr-1"></i>
                                        Ver
                                    </a>
                                    
                                    @if($ticket->status === 'pendiente')
                                        <a href="{{ route('tickets.edit', $ticket) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-edit mr-1"></i>
                                            Editar
                                        </a>
                                    @endif

                                    @if($ticket->status === 'finalizado' && $ticket->survey && !$ticket->survey->completed_at)
                                        <a href="{{ route('tickets.show', $ticket) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-yellow-600 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                            <i class="fas fa-star mr-1"></i>
                                            Calificar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($tickets->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $tickets->links() }}
                    </div>
                @endif
            @else
                <!-- Estado vacío -->
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400">
                        <i class="fas fa-inbox text-6xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tienes tickets</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Comienza creando tu primera solicitud de movimiento de carga.
                    </p>
                    @if(auth()->user()->canCreateTicket())
                        <div class="mt-6">
                            <a href="{{ route('tickets.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent shadow-sm text-sm font-medium rounded-md text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-plus mr-2"></i>
                                Crear mi primer ticket
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtro por estado
    document.getElementById('status-filter').addEventListener('change', function() {
        filterTickets();
    });

    // Filtro por fecha
    document.getElementById('date-filter').addEventListener('change', function() {
        filterTickets();
    });

    function filterTickets() {
        const statusFilter = document.getElementById('status-filter').value;
        const dateFilter = document.getElementById('date-filter').value;
        
        // Aquí puedes implementar la lógica de filtrado
        // Por ahora, puedes recargar la página con parámetros de consulta
        const url = new URL(window.location.href);
        
        if (statusFilter) {
            url.searchParams.set('status', statusFilter);
        } else {
            url.searchParams.delete('status');
        }
        
        if (dateFilter) {
            url.searchParams.set('date', dateFilter);
        } else {
            url.searchParams.delete('date');
        }
        
        window.location.href = url.toString();
    }
});
</script>
@endpush
@endsection