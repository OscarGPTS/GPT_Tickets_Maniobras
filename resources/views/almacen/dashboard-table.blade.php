@extends('layouts.app')

@section('title', 'Panel de Almacén')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Panel de Almacén </h1>
            <p class="mt-1 text-sm text-gray-600">
                Gestiona y responde a las solicitudes de servicio
            </p>
        </div>

        <!-- Métricas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Tickets Pendientes -->
            <div class="bg-gradient-to-br from-blue-400 to-indigo-600 rounded-lg shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Tickets Pendientes</p>
                        <p class="text-4xl font-bold mt-2">{{ $myTickets->total() }}</p>
                        <p class="text-blue-100 text-sm mt-1">Trabajando en estos</p>
                    </div>
                    <div class="bg-blue-500 bg-opacity-30 rounded-full p-4">
                        <i class="fas fa-tasks text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completados hoy -->
            <div class="bg-gradient-to-br from-green-400 to-emerald-600 rounded-lg shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Todos mis Tickets</p>
                        <p class="text-4xl font-bold mt-2">{{ $allMyTickets->count() }}</p>
                    </div>
                    <div class="bg-green-500 bg-opacity-30 rounded-full p-4">
                        <i class="fas fa-check-circle text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs de navegación -->
        <div class="bg-white rounded-t-lg shadow-sm border-b border-gray-200 mb-0">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('nuevos')" id="tab-nuevos" class="tab-button active-tab border-b-2 border-yellow-500 py-4 px-1 text-sm font-medium text-yellow-600 whitespace-nowrap">
                    <i class="fas fa-inbox mr-2"></i>
                    Tickets Pendientes
                    <span class="ml-2 bg-yellow-100 text-yellow-600 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $myTickets->total() }}</span>
                </button>
                
                <button onclick="showTab('todos')" id="tab-todos" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    <i class="fas fa-user-check mr-2"></i>
                    Todos mis Tickets
                    <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $allMyTickets->count() }}</span>
                </button>
            </nav>
        </div>

        <div id="tab-content-nuevos" class="tab-content">
            <div class="bg-white rounded-b-lg shadow-sm border-l border-r border-b border-gray-200 p-6">

                @if($myTickets->count() > 0)
                    <div class="grid gap-4">
                        @foreach($myTickets as $ticket)
                            <div class="bg-white rounded-lg shadow-sm border-l-4 border-yellow-400 hover:shadow-md transition-shadow duration-200">
                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            
                                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                                <span class="text-indigo-600">#{{ $ticket->id }}</span> - {{ $ticket->title }}
                                            </h3>
                                            
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                                {{ $ticket->description }}
                                            </p>
                                            
                                            <div class="flex items-center text-sm text-gray-500">
                                                <img class="h-6 w-6 rounded-full mr-2" src="{{ $ticket->user->getAvatarUrl() }}" alt="">
                                                <span class="font-medium">{{ $ticket->user->name }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col space-y-2 ml-4">
                                            <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                               class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Ver Ticket
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    @if($myTickets->hasPages())
                        <div class="mt-6">
                            {{ $myTickets->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>


        <!-- Mis Tickets en Proceso -->
        <div id="tab-content-todos" class="tab-content hidden">
            <div class="bg-white rounded-b-lg shadow-sm border-l border-r border-b border-gray-200 p-6">           
                @if($allMyTickets->count() > 0)
                    <div class="grid gap-4">
                        @foreach($allMyTickets as $ticket)
                            <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-400 hover:shadow-md transition-shadow duration-200">
                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-3">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 uppercase">
                                                    <i class="fas fa-spinner mr-2"></i>
                                                    En Proceso
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    <i class="far fa-clock mr-1"></i>
                                                    Asignado {{ $ticket->assigned_at->diffForHumans() }}
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
                                            
                                            <div class="flex items-center text-sm text-gray-500">
                                                <img class="h-6 w-6 rounded-full mr-2" src="{{ $ticket->user->getAvatarUrl() }}" alt="">
                                                <span class="font-medium">Solicitado por: {{ $ticket->user->name }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="ml-4">
                                            
                                            <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                               class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-eye mr-2"></i>
                                                Ver Ticket
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Paginación -->
                    @if($allMyTickets->hasPages())
                        <div class="mt-6">
                            {{ $allMyTickets->links() }}
                        </div>
                    @endif
                    
                @else
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border-2 border-dashed border-blue-300 p-12 text-center">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-clipboard-list text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Sin tickets</h3>
                        <p class="text-blue-700">No tienes tickets en proceso en este momento.</p>
                    </div>
                @endif
            </div>
        </div>


    </div>
</div>

@push('scripts')
<script>
function showTab(tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Remover estilos activos de todos los botones
    document.querySelectorAll('.tab-button').forEach(el => {
        el.classList.remove('border-yellow-500', 'text-yellow-600', 'border-blue-500', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mostrar el contenido seleccionado
    document.getElementById('tab-content-' + tabName).classList.remove('hidden');
    
    // Activar el botón seleccionado
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    
    if (tabName === 'nuevos') {
        activeButton.classList.add('border-yellow-500', 'text-yellow-600');
    } else if (tabName === 'todos') {
        activeButton.classList.add('border-blue-500', 'text-blue-600');
    }
}
</script>
@endpush

@endsection
