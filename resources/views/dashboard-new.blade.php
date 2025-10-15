@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            ¡Hola, {{ Auth::user()->name }}! 👋
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
                <a href="{{ route('tickets.create') }}" 
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

    <!-- Tickets Activos (Pendientes + En Proceso) -->
    @if($activeTickets->count() > 0)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="bg-blue-100 text-blue-800 rounded-full p-2 mr-3">
                        <i class="fas fa-tasks"></i>
                    </span>
                    Tickets Activos
                    <span class="ml-3 text-sm font-normal text-gray-500">({{ $activeTickets->count() }})</span>
                </h2>
                <a href="{{ route('tickets.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    Ver todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid gap-4">
                @foreach($activeTickets as $ticket)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 {{ $ticket->isPendiente() ? 'border-yellow-400' : 'border-blue-400' }} hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $ticket->getStatusBadgeClass() }} uppercase">
                                            @if($ticket->isPendiente())
                                                <i class="fas fa-clock mr-2"></i>
                                                Pendiente
                                            @else
                                                <i class="fas fa-spinner mr-2"></i>
                                                En Proceso
                                            @endif
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            <i class="far fa-calendar mr-1"></i>
                                            {{ $ticket->created_at->diffForHumans() }}
                                        </span>
                                        @if($ticket->images->count() > 0)
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-images mr-1"></i>
                                                {{ $ticket->images->count() }} fotos
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                                        <span class="text-indigo-600">#{{ $ticket->id }}</span> - {{ $ticket->title }}
                                    </h3>
                                    
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                        {{ $ticket->description }}
                                    </p>
                                    
                                    @if($ticket->assigned_to)
                                        <div class="flex items-center text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2 inline-flex">
                                            <i class="fas fa-user-check text-green-600 mr-2"></i>
                                            <span class="font-medium">Asignado a:</span>
                                            <span class="ml-1">{{ $ticket->assignedTo->name }}</span>
                                        </div>
                                    @else
                                        <div class="flex items-center text-sm text-yellow-700 bg-yellow-50 rounded-lg px-3 py-2 inline-flex">
                                            <i class="fas fa-hourglass-half mr-2"></i>
                                            Esperando asignación
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex flex-col space-y-2 ml-4">
                                    <a href="{{ route('tickets.show', $ticket) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-eye mr-2"></i>
                                        Ver Ticket
                                    </a>
                                    @if($ticket->canBeCancelled())
                                        <button onclick="openCancelModal({{ $ticket->id }})" 
                                                class="inline-flex items-center justify-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                            <i class="fas fa-times mr-2"></i>
                                            Cancelar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Tickets Completados -->
    @if($completedTickets->count() > 0)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="bg-green-100 text-green-800 rounded-full p-2 mr-3">
                        <i class="fas fa-check-circle"></i>
                    </span>
                    Tickets Completados Recientemente
                    <span class="ml-3 text-sm font-normal text-gray-500">({{ $completedTickets->count() }})</span>
                </h2>
                <a href="{{ route('tickets.index', ['status' => 'finalizado']) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    Ver todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid gap-4">
                @foreach($completedTickets as $ticket)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-400 hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 uppercase">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Finalizado
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            <i class="far fa-clock mr-1"></i>
                                            Completado {{ $ticket->completed_at->diffForHumans() }}
                                        </span>
                                        @if($ticket->survey)
                                            @if($ticket->survey->completed_at)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-star mr-1"></i>
                                                    {{ $ticket->survey->rating }}/5
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    Calificación pendiente
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                                        <span class="text-indigo-600">#{{ $ticket->id }}</span> - {{ $ticket->title }}
                                    </h3>
                                    
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                        {{ $ticket->description }}
                                    </p>
                                    
                                    <div class="flex items-center text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2 inline-flex">
                                        <i class="fas fa-user-check text-green-600 mr-2"></i>
                                        <span class="font-medium">Completado por:</span>
                                        <span class="ml-1">{{ $ticket->assignedTo->name }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col space-y-2 ml-4">
                                    <a href="{{ route('tickets.show', $ticket) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <i class="fas fa-eye mr-2"></i>
                                        Ver Detalles
                                    </a>
                                    @if($ticket->survey && !$ticket->survey->completed_at)
                                        <a href="{{ route('tickets.show', $ticket) }}#survey-form" 
                                           class="inline-flex items-center justify-center px-4 py-2 border border-yellow-600 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50 animate-pulse">
                                            <i class="fas fa-star mr-2"></i>
                                            Calificar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Encuestas pendientes -->
    @if($pendingSurveys->count() > 0)
        <div id="pending-surveys" class="mb-8">
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
                                    Ticket #{{ $survey->ticket->id }} - {{ $survey->ticket->title }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Completado por {{ $survey->ticket->assignedTo->name }}
                                </p>
                            </div>
                            <a href="{{ route('tickets.show', $survey->ticket) }}#survey-form" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition-colors">
                                <i class="fas fa-star mr-2"></i>
                                Calificar Ahora
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Estado vacío -->
    @if($activeTickets->count() === 0 && $completedTickets->count() === 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                <i class="fas fa-inbox text-6xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes tickets todavía</h3>
            <p class="text-sm text-gray-500 mb-6">
                Crea tu primera solicitud de servicio para comenzar.
            </p>
            @if($canCreateTicket)
                <a href="{{ route('tickets.create') }}" 
                   class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i>
                    Nueva Solicitud
                </a>
            @endif
        </div>
    @endif
</div>

<!-- Modal de cancelación -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Cancelar Ticket</h3>
                <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="cancelForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Razón de cancelación <span class="text-red-500">*</span>
                    </label>
                    <textarea name="cancellation_reason" id="cancellation_reason" rows="4" 
                             class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                             placeholder="Explica brevemente por qué cancelas este ticket..."
                             required></textarea>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeCancelModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                        Confirmar Cancelación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openCancelModal(ticketId) {
    const form = document.getElementById('cancelForm');
    form.action = `/tickets/${ticketId}/cancel`;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancellation_reason').value = '';
}

// Cerrar modal al hacer clic fuera
document.getElementById('cancelModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCancelModal();
    }
});
</script>
@endpush
@endsection
