@extends('layouts.app')

@section('title', 'Mis Tickets')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header con saludo -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            ¡Hola, {{ Auth::user()->name }}!
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Bienvenido a tu panel de tickets de movimiento de carga
        </p>
    </div>

    <!-- Widget de nueva solicitud o alerta de encuestas -->
    @if($canCreateTicket)
        <div class="bg-orange-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-xl font-bold mb-2">Nueva Solicitud</h3>
                    <p>Crea una nueva solicitud y nuestro equipo de almacén la atenderá lo antes posible.</p>
                </div>
                <a href="{{ route('solicitante.tickets.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-red-600 font-semibold rounded-lg hover:bg-indigo-50 transition-colors shadow-md ml-4">
                    <i class="fas fa-plus mr-2"></i>
                    Nueva Solicitud
                </a>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <span class="font-medium">Tienes encuestas pendientes.</span> 
                        Por favor completa las encuestas de tus tickets finalizados antes de crear nuevas solicitudes.
                    </p>
                    <a href="#pending-surveys" class="text-sm font-medium text-yellow-800 hover:text-yellow-900 underline mt-1 inline-block">
                        Ver encuestas pendientes →
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <a href="{{ route('solicitante.tickets.index') }}" 
           class="bg-white rounded-lg shadow-sm border-2 {{ request('status') === null ? 'border-indigo-500' : 'border-gray-200' }} p-4 hover:shadow-md transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Todos</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ Auth::user()->tickets->count() }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i class="fas fa-list text-indigo-600 text-xl"></i>
                </div>
            </div>
        </a>
        
        <a href="{{ route('solicitante.tickets.index', ['status' => 'pendiente']) }}" 
           class="bg-white rounded-lg shadow-sm border-2 {{ request('status') === 'pendiente' ? 'border-yellow-500' : 'border-gray-200' }} p-4 hover:shadow-md transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-yellow-800 uppercase tracking-wide">Por aprobar</p>
                    <p class="text-2xl font-bold text-yellow-900 mt-1">{{ Auth::user()->tickets()->where('status', 'pendiente')->count() }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </a>
        
        <a href="{{ route('solicitante.tickets.index', ['status' => 'en_proceso']) }}" 
           class="bg-white rounded-lg shadow-sm border-2 {{ request('status') === 'en_proceso' ? 'border-blue-500' : 'border-gray-200' }} p-4 hover:shadow-md transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-blue-800 uppercase tracking-wide">En Proceso</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ Auth::user()->tickets()->where('status', 'en_proceso')->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-spinner text-blue-600 text-xl"></i>
                </div>
            </div>
        </a>
        
        <a href="{{ route('solicitante.tickets.index', ['status' => 'finalizado']) }}" 
           class="bg-white rounded-lg shadow-sm border-2 {{ request('status') === 'finalizado' ? 'border-green-500' : 'border-gray-200' }} p-4 hover:shadow-md transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-green-800 uppercase tracking-wide">Finalizados</p>
                    <p class="text-2xl font-bold text-green-900 mt-1">{{ Auth::user()->tickets()->where('status', 'finalizado')->count() }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </a>
        
        <a href="{{ route('solicitante.tickets.index', ['status' => 'cancelado']) }}" 
           class="bg-white rounded-lg shadow-sm border-2 {{ request('status') === 'cancelado' ? 'border-red-500' : 'border-gray-200' }} p-4 hover:shadow-md transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-red-800 uppercase tracking-wide">Cancelados</p>
                    <p class="text-2xl font-bold text-red-900 mt-1">{{ Auth::user()->tickets()->where('status', 'cancelado')->count() }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </a>
    </div>


    <!-- Encuestas pendientes -->
    @if($pendingSurveys->count() > 0)
        <div id="pending-surveys" class="mt-8">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <span class="bg-yellow-100 text-yellow-800 rounded-full p-2 mr-3">
                        <i class="fas fa-star"></i>
                    </span>
                    <h2 class="text-xl font-bold text-gray-900">
                        Encuestas Pendientes
                        <span class="ml-2 text-sm font-normal text-gray-600">({{ $pendingSurveys->count() }})</span>
                    </h2>
                </div>
                <p class="text-sm text-gray-700 mb-4">
                    Por favor califica los siguientes servicios para poder crear nuevas solicitudes:
                </p>
                <div class="space-y-3">
                    @foreach($pendingSurveys as $survey)
                        <div class="bg-white rounded-lg border border-yellow-200 p-4 flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">
                                    Ticket #{{ $survey->ticket->id }} - {{ Str::limit($survey->ticket->title, 60) }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Completado por {{ $survey->ticket->assignedTo->name }}
                                </p>
                            </div>
                            <a href="{{ route('solicitante.tickets.show', $survey->ticket) }}#survey-form" 
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 text-gray-900 font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-star mr-2"></i>
                                Calificar Ahora
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <br>
    <!-- Tabla de tickets -->
    @if($tickets->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <!-- Header de tabla -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Lista de Tickets
                        @if(request('status'))
                            <span class="ml-2 text-sm font-normal text-gray-600">
                                - {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                            </span>
                        @endif
                    </h2>
                    <span class="text-sm text-gray-600">
                        Total: <span class="font-semibold text-indigo-600">{{ $tickets->total() }}</span> tickets
                    </span>
                </div>
            </div>

            <!-- Tabla responsive -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID 
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Información
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fechas
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hide-mobile">
                                Asignación
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hide-mobile">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $ticket->created_at->diffInHours() < 24 ? 'is-new' : '' }}" 
                                data-ticket-id="{{ $ticket->id }}">
                                <!-- ID y Estado -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-bold text-gray-900">
                                                {{ $ticket->created_at->format('Y') }}-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                        
                                    </div>
                                </td>

                                <!-- Información del ticket -->
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <div class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                                            {{ Str::limit($ticket->title, 50) }}
                                        </div>
                                        <div class="text-xs text-gray-600 line-clamp-2">
                                            {{ Str::limit($ticket->description, 80) }}
                                        </div>
                                        <div class="flex items-center space-x-3 mt-2">
                                            @if($ticket->images->count() > 0)
                                                <span class="inline-flex items-center text-xs text-gray-500">
                                                    <i class="fas fa-images mr-1 text-blue-500"></i>
                                                    <span class="font-medium">{{ $ticket->images->count() }}</span> fotos
                                                </span>
                                            @endif
                                            @if($ticket->survey)
                                                @if($ticket->survey->completed_at)
                                                    <span class="inline-flex items-center text-xs text-green-600">
                                                        <i class="fas fa-star mr-1"></i>
                                                        Calificado: {{ $ticket->survey->rating }}/5
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center text-xs text-yellow-600 animate-pulse">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                                        Pendiente de calificar
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Fechas -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs space-y-1">
                                        <div class="text-gray-900 font-medium">
                                            <i class="far fa-calendar-plus mr-1 text-green-500"></i>
                                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{ $ticket->created_at->diffForHumans() }}
                                        </div>
                                        @if($ticket->assigned_at)
                                            <div class="text-blue-600 mt-2">
                                                <i class="far fa-clock mr-1"></i>
                                                Asignado: {{ $ticket->assigned_at->format('d/m/Y') }}
                                            </div>
                                        @endif
                                        @if($ticket->completed_at)
                                            <div class="text-green-600 mt-2">
                                                <i class="fas fa-check mr-1"></i>
                                                Completado: {{ $ticket->completed_at->format('d/m/Y') }}
                                            </div>
                                        @endif
                                        @if($ticket->cancelled_at)
                                            <div class="text-red-600 mt-2">
                                                <i class="fas fa-ban mr-1"></i>
                                                Cancelado: {{ $ticket->cancelled_at->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Asignación -->
                                <td class="px-6 py-4 whitespace-nowrap text-start">
                                    @if($ticket->assigned_to)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ Str::limit($ticket->assignedTo->name, 20) }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400 italic">
                                            Sin asignar
                                        </div>
                                    @endif
                                </td>


                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $ticket->getStatusBadgeClass() }} uppercase shadow-sm status-badge">
                                        {{ $ticket->getStatusText() }}
                                    </span>
                                </td>
                                <!-- Acciones -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('solicitante.tickets.show', $ticket) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-150"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer con paginación -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Mostrando 
                        <span class="font-medium">{{ $tickets->firstItem() ?? 0 }}</span>
                        a
                        <span class="font-medium">{{ $tickets->lastItem() ?? 0 }}</span>
                        de
                        <span class="font-medium">{{ $tickets->total() }}</span>
                        resultados
                    </div>
                    <div>
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Estado vacío -->
        <div class="bg-white rounded-lg shadow-md border-2 border-dashed border-gray-300 p-12 text-center">
            <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                <svg class="h-full w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                @if(request('status'))
                    No hay tickets {{ str_replace('_', ' ', request('status')) }}
                @else
                    No tienes tickets creados
                @endif
            </h3>
            <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                @if(request('status'))
                    No se encontraron tickets con el estado "{{ str_replace('_', ' ', request('status')) }}". Intenta con otro filtro.
                @else
                    Comienza creando tu primera solicitud de movimiento de carga. El equipo de almacén será notificado automáticamente.
                @endif
            </p>
            @if(!request('status'))
                <a href="{{ route('solicitante.tickets.create') }}" 
                   class="inline-flex items-center px-6 py-3 border border-transparent shadow-lg text-base font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear Primera Solicitud
                </a>
            @else
                <a href="{{ route('solicitante.tickets.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Ver todos los tickets
                </a>
            @endif
        </div>
    @endif
</div>


</div>

@endsection


