@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            ¡Hola, {{ Auth::user()->name }}!
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Bienvenido a tu panel de control de tickets
        </p>
    </div>

    <!-- Métricas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Total de tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_tickets'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Tickets creados</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <i class="fas fa-clipboard-list text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>

        <!-- Pendientes -->
        <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium uppercase tracking-wide">Pendientes</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['pending_tickets'] }}</p>
                    <p class="text-xs text-yellow-100 mt-1">Sin asignar</p>
                </div>
                <div class="bg-yellow-500 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- En proceso -->
        <div class="bg-gradient-to-br from-blue-400 to-indigo-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">En Proceso</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['in_progress_tickets'] }}</p>
                    <p class="text-xs text-blue-100 mt-1">Siendo atendidos</p>
                </div>
                <div class="bg-blue-500 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-spinner text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Completados -->
        <div class="bg-gradient-to-br from-green-400 to-emerald-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Completados</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['completed_tickets'] }}</p>
                    <p class="text-xs text-green-100 mt-1">Finalizados</p>
                </div>
                <div class="bg-green-500 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Acción principal -->
    @if($canCreateTicket)
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-xl font-bold mb-2">¿Necesitas ayuda?</h3>
                    <p class="text-indigo-100">Crea una nueva solicitud de servicio y nuestro equipo te atenderá lo antes posible.</p>
                </div>
                <a href="{{ route('solicitante.tickets.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-indigo-50 transition-colors shadow-md ml-4">
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

    <!-- Tabla de Todos los Tickets -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="bg-indigo-100 text-indigo-800 rounded-full p-2 mr-3">
                        <i class="fas fa-list"></i>
                    </span>
                    Mis Tickets
                    <span class="ml-3 text-sm font-normal text-gray-500">({{ $tickets->total() }} total)</span>
                </h2>
                <a href="{{ route('solicitante.tickets.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Nuevo Ticket
                </a>
            </div>
        </div>

        @if($tickets->count() > 0)
            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ticket
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Asignado a
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Encuesta
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <!-- Estado -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $ticket->getStatusBadgeClass() }}">
                                        @if($ticket->isPendiente())
                                            <i class="fas fa-clock mr-1.5"></i>
                                            Pendiente
                                        @elseif($ticket->status === 'en_proceso')
                                            <i class="fas fa-spinner mr-1.5"></i>
                                            En Proceso
                                        @elseif($ticket->status === 'finalizado')
                                            <i class="fas fa-check-circle mr-1.5"></i>
                                            Completado
                                        @else
                                            <i class="fas fa-times-circle mr-1.5"></i>
                                            Cancelado
                                        @endif
                                    </span>
                                </td>

                                <!-- Información del Ticket -->
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        @if($ticket->images->count() > 0)
                                            <div class="flex-shrink-0">
                                                <img src="{{ $ticket->images->first()->url }}" 
                                                     alt="Ticket" 
                                                     class="h-12 w-12 rounded-lg object-cover border border-gray-200">
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $ticket->description }}
                                            </p>
                                            <div class="flex items-center space-x-3 mt-1">
                                                <span class="text-xs text-gray-500">
                                                    <i class="fas fa-hashtag"></i>
                                                    {{ $ticket->id }}
                                                </span>
                                                @if($ticket->images->count() > 0)
                                                    <span class="text-xs text-gray-500">
                                                        <i class="fas fa-images"></i>
                                                        {{ $ticket->images->count() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Asignado a -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->assignedTo)
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                                <span class="text-white text-xs font-semibold">
                                                    {{ $ticket->assignedTo->getInitials() }}
                                                </span>
                                            </div>
                                            <div class="ml-2">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $ticket->assignedTo->name }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 italic">
                                            Sin asignar
                                        </span>
                                    @endif
                                </td>

                                <!-- Fecha -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $ticket->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $ticket->created_at->format('H:i') }}
                                    </div>
                                </td>

                                <!-- Encuesta -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($ticket->survey)
                                        @if($ticket->survey->isCompleted())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-star mr-1"></i>
                                                {{ $ticket->survey->rating }}/5
                                            </span>
                                        @else
                                            <a href="{{ route('solicitante.tickets.show', $ticket) }}#survey-form"
                                               class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition-colors">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                Pendiente
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                <!-- Acciones -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('solicitante.tickets.show', $ticket) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <i class="fas fa-eye mr-1.5"></i>
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $tickets->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-inbox text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    No tienes tickets aún
                </h3>
                <p class="text-sm text-gray-500 mb-6">
                    Comienza creando tu primer ticket de soporte
                </p>
                <a href="{{ route('solicitante.tickets.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Primer Ticket
                </a>
            </div>
        @endif
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
                                    Ticket #{{ $survey->ticket->id }} - {{ $survey->ticket->description }}
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
</div>
@endsection
