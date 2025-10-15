@extends('layouts.app')

@section('title', 'Mis Tickets')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header con acciones -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Solicitudes</h1>
            <p class="mt-1 text-sm text-gray-500">
                Gestiona y realiza seguimiento de tus tickets
            </p>
        </div>
        <a href="{{ route('tickets.create') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
            <i class="fas fa-plus mr-2"></i>
            Nueva Solicitud
        </a>
    </div>

    <!-- Filtros rápidos -->
    <div class="mb-6 flex items-center space-x-2 overflow-x-auto pb-2">
        <a href="{{ route('tickets.index') }}" 
           class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === null ? 'bg-indigo-100 text-indigo-700' : 'bg-white text-gray-600 hover:bg-gray-50' }} border {{ request('status') === null ? 'border-indigo-200' : 'border-gray-200' }}">
            <i class="fas fa-list mr-2"></i>
            Todos
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100">{{ Auth::user()->tickets->count() }}</span>
        </a>
        <a href="{{ route('tickets.index', ['status' => 'pendiente']) }}" 
           class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-white text-gray-600 hover:bg-gray-50' }} border {{ request('status') === 'pendiente' ? 'border-yellow-200' : 'border-gray-200' }}">
            <i class="fas fa-clock mr-2"></i>
            Pendientes
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">{{ Auth::user()->tickets()->where('status', 'pendiente')->count() }}</span>
        </a>
        <a href="{{ route('tickets.index', ['status' => 'en_proceso']) }}" 
           class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'en_proceso' ? 'bg-blue-100 text-blue-700' : 'bg-white text-gray-600 hover:bg-gray-50' }} border {{ request('status') === 'en_proceso' ? 'border-blue-200' : 'border-gray-200' }}">
            <i class="fas fa-spinner mr-2"></i>
            En Proceso
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ Auth::user()->tickets()->where('status', 'en_proceso')->count() }}</span>
        </a>
        <a href="{{ route('tickets.index', ['status' => 'finalizado']) }}" 
           class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'finalizado' ? 'bg-green-100 text-green-700' : 'bg-white text-gray-600 hover:bg-gray-50' }} border {{ request('status') === 'finalizado' ? 'border-green-200' : 'border-gray-200' }}">
            <i class="fas fa-check-circle mr-2"></i>
            Finalizados
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">{{ Auth::user()->tickets()->where('status', 'finalizado')->count() }}</span>
        </a>
        <a href="{{ route('tickets.index', ['status' => 'cancelado']) }}" 
           class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'cancelado' ? 'bg-red-100 text-red-700' : 'bg-white text-gray-600 hover:bg-gray-50' }} border {{ request('status') === 'cancelado' ? 'border-red-200' : 'border-gray-200' }}">
            <i class="fas fa-times-circle mr-2"></i>
            Cancelados
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">{{ Auth::user()->tickets()->where('status', 'cancelado')->count() }}</span>
        </a>
    </div>

    <!-- Lista de tickets -->
    @if($tickets->count() > 0)
        <div class="grid gap-4">
            @foreach($tickets as $ticket)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <!-- Info principal -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getStatusBadgeClass() }}">
                                        {{ $ticket->getStatusText() }}
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
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    #{{ $ticket->id }} - {{ $ticket->title }}
                                </h3>
                                
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                    {{ $ticket->description }}
                                </p>
                                
                                <!-- Info adicional -->
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                    @if($ticket->assigned_to)
                                        <span class="flex items-center">
                                            <i class="fas fa-user mr-2"></i>
                                            Asignado a: <span class="font-medium ml-1">{{ $ticket->assignedTo->name }}</span>
                                        </span>
                                    @endif
                                    
                                    @if($ticket->survey && !$ticket->survey->completed_at)
                                        <span class="flex items-center text-yellow-600">
                                            <i class="fas fa-star mr-2"></i>
                                            Pendiente de calificar
                                        </span>
                                    @elseif($ticket->survey && $ticket->survey->completed_at)
                                        <span class="flex items-center text-green-600">
                                            <i class="fas fa-star mr-2"></i>
                                            Calificado: {{ $ticket->survey->rating }}/5
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Acciones -->
                            <div class="flex items-start space-x-2 ml-4">
                                <a href="{{ route('tickets.show', $ticket) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-eye mr-1"></i>
                                    Ver
                                </a>
                                
                                @if($ticket->canBeCancelled())
                                    <button onclick="openCancelModal({{ $ticket->id }})" 
                                            class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <i class="fas fa-times mr-1"></i>
                                        Cancelar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    @else
        <!-- Estado vacío -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                <i class="fas fa-inbox text-6xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes tickets</h3>
            <p class="text-sm text-gray-500 mb-6">
                Comienza creando tu primera solicitud de servicio.
            </p>
            <a href="{{ route('tickets.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>
                Nueva Solicitud
            </a>
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
