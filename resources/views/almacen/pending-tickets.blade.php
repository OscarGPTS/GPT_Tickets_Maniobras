@extends('layouts.app')

@section('title', 'Tickets Pendientes - Almacén')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Tickets Pendientes</h1>
                        <p class="text-gray-600 mt-2">Gestiona y asigna tickets que están esperando atención</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="document.getElementById('exportForm').submit()" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-green-700">
                            <i class="fas fa-file-excel mr-2"></i>
                            Exportar Excel
                        </button>
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

        <!-- Formulario de exportación (oculto) -->
        <form id="exportForm" action="{{ route('almacen.tickets.export') }}" method="GET" class="hidden">
            <input type="hidden" name="status" value="pendiente">
        </form>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $tickets->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Nuevos Hoy</p>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Ticket::where('status', 'pendiente')->whereDate('created_at', today())->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Promedio Tiempo</p>
                        <p class="text-2xl font-bold text-gray-900">2.5h</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Tickets Pendientes</h3>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">{{ $tickets->count() }} de {{ $tickets->total() }} tickets</span>
                    </div>
                </div>
            </div>
            
            <div class="divide-y divide-gray-200">
                @forelse($tickets as $ticket)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        #{{ $ticket->id }} - {{ $ticket->title }}
                                    </h4>
                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                        Pendiente
                                    </span>
                                </div>

                                <p class="text-gray-700 mb-3">{{ Str::limit($ticket->description, 200) }}</p>

                                <div class="flex items-center space-x-6 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>{{ $ticket->user->name }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($ticket->solicitudImages->count() > 0)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>{{ $ticket->solicitudImages->count() }} imagen(es)</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col space-y-2 ml-6">
                                <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver Detalles
                                </a>
                                
                                @if(auth()->user()->isAlmacen())
                                    <!-- Botón auto-asignación para personal de almacén -->
                                    <form method="POST" action="{{ route('almacen.tickets.assign', $ticket) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Asignar a Mí
                                        </button>
                                    </form>
                                @else
                                    <!-- Botón para abrir modal de asignación (Admin) -->
                                    <button type="button" 
                                            onclick="openAssignModal({{ $ticket->id }}, '{{ addslashes($ticket->title) }}')"
                                            class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Asignar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">¡Excelente trabajo!</h3>
                        <p class="mt-1 text-sm text-gray-500">No hay tickets pendientes por asignar en este momento</p>
                        <div class="mt-6">
                            <a href="{{ route('almacen.dashboard') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Volver al Dashboard
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Modal de Asignación -->
@if(auth()->user()->isAdmin() && $almacenUsers)
<div id="assignModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-user-plus text-green-600 mr-2"></i>
                    Asignar Ticket
                </h3>
                <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Información del ticket -->
            <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-gray-600">Ticket:</p>
                <p class="font-semibold text-gray-900" id="modalTicketTitle"></p>
            </div>

            <!-- Formulario -->
            <form id="assignForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar responsable:
                    </label>
                    <select id="assigned_to" 
                            name="assigned_to" 
                            required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                        <option value="">-- Seleccionar personal de almacén --</option>
                        @foreach($almacenUsers as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="closeAssignModal()"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAssignModal(ticketId, ticketTitle) {
    document.getElementById('assignModal').classList.remove('hidden');
    document.getElementById('modalTicketTitle').textContent = '#' + ticketId + ' - ' + ticketTitle;
    document.getElementById('assignForm').action = '/almacen/tickets/' + ticketId + '/assign';
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    document.getElementById('assigned_to').value = '';
}

// Cerrar modal al hacer clic fuera
document.getElementById('assignModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAssignModal();
    }
});

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAssignModal();
    }
});
</script>
@endif

@endsection
