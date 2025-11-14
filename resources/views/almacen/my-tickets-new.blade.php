@extends('layouts.app')

@section('title', 'Mis Tickets - Almacén')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <span class="bg-indigo-100 text-indigo-800 rounded-full p-2 mr-3">
                        <i class="fas fa-clipboard-list"></i>
                    </span>
                    Mis Tickets Asignados
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Gestiona todos los tickets que tienes asignados
                </p>
            </div>
            <a href="{{ route('almacen.dashboard') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Métricas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Total Asignados -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $tickets->total() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Tickets asignados</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <i class="fas fa-clipboard-list text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <!-- En Proceso -->
        <div class="bg-gradient-to-br from-blue-400 to-indigo-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">En Proceso</p>
                    <p class="text-3xl font-bold mt-2">{{ \App\Models\Ticket::where('assigned_to', auth()->id())->where('status', \App\Models\Ticket::STATUS_EN_PROCESO)->count() }}</p>
                    <p class="text-xs text-blue-100 mt-1">Trabajando ahora</p>
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
                    <p class="text-3xl font-bold mt-2">{{ \App\Models\Ticket::where('assigned_to', auth()->id())->where('status', \App\Models\Ticket::STATUS_FINALIZADO)->count() }}</p>
                    <p class="text-xs text-green-100 mt-1">Total finalizados</p>
                </div>
                <div class="bg-green-500 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Hoy -->
        <div class="bg-gradient-to-br from-orange-400 to-red-500 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Hoy</p>
                    <p class="text-3xl font-bold mt-2">{{ \App\Models\Ticket::where('assigned_to', auth()->id())->whereDate('updated_at', today())->count() }}</p>
                    <p class="text-xs text-orange-100 mt-1">Tickets actualizados</p>
                </div>
                <div class="bg-orange-500 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de estado -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center space-x-2 overflow-x-auto">
            <a href="{{ route('almacen.tickets.mine') }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-list mr-2"></i>
                Todos
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ !request('status') ? 'bg-indigo-500' : 'bg-gray-200' }}">
                    {{ $tickets->total() }}
                </span>
            </a>
            <a href="{{ route('almacen.tickets.mine', ['status' => \App\Models\Ticket::STATUS_EN_PROCESO]) }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === \App\Models\Ticket::STATUS_EN_PROCESO ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-spinner mr-2"></i>
                En Proceso
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ request('status') === \App\Models\Ticket::STATUS_EN_PROCESO ? 'bg-blue-500' : 'bg-gray-200' }}">
                    {{ \App\Models\Ticket::where('assigned_to', auth()->id())->where('status', \App\Models\Ticket::STATUS_EN_PROCESO)->count() }}
                </span>
            </a>
            <a href="{{ route('almacen.tickets.mine', ['status' => \App\Models\Ticket::STATUS_FINALIZADO]) }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === \App\Models\Ticket::STATUS_FINALIZADO ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-check-circle mr-2"></i>
                Completados
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ request('status') === \App\Models\Ticket::STATUS_FINALIZADO ? 'bg-green-500' : 'bg-gray-200' }}">
                    {{ \App\Models\Ticket::where('assigned_to', auth()->id())->where('status', \App\Models\Ticket::STATUS_FINALIZADO)->count() }}
                </span>
            </a>
        </div>
    </div>

    <!-- Lista de Tickets -->
    <div class="space-y-4">
        @forelse($tickets as $ticket)
            <div class="bg-white rounded-lg shadow-sm border-l-4 {{ $ticket->isEnProceso() ? 'border-blue-400' : ($ticket->isFinalizado() ? 'border-green-400' : 'border-gray-400') }} hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $ticket->getStatusBadgeClass() }} uppercase">
                                    @if($ticket->isEnProceso())
                                        <i class="fas fa-spinner mr-2"></i>
                                        En Proceso
                                    @elseif($ticket->isFinalizado())
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Completado
                                    @else
                                        <i class="fas fa-clock mr-2"></i>
                                        {{ $ticket->status }}
                                    @endif
                                </span>
                                
                                @if($ticket->priority === 'alta')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Alta Prioridad
                                    </span>
                                @elseif($ticket->priority === 'media')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Media Prioridad
                                    </span>
                                @endif

                                <span class="text-sm text-gray-500">
                                    <i class="far fa-calendar mr-1"></i>
                                    Asignado {{ $ticket->assigned_at ? $ticket->assigned_at->diffForHumans() : 'hace poco' }}
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
                            
                            <div class="flex items-center space-x-4 text-sm">
                                <div class="flex items-center text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                    <i class="fas fa-user text-gray-500 mr-2"></i>
                                    <span class="font-medium">Solicitante:</span>
                                    <span class="ml-1">{{ $ticket->user->name }}</span>
                                </div>
                                
                                @if($ticket->work_evidence)
                                    <div class="flex items-center text-green-700 bg-green-50 rounded-lg px-3 py-2">
                                        <i class="fas fa-clipboard-check text-green-600 mr-2"></i>
                                        <span class="font-medium">Evidencia agregada</span>
                                    </div>
                                @endif

                                @if($ticket->completed_at)
                                    <div class="flex items-center text-gray-500">
                                        <i class="far fa-clock mr-1"></i>
                                        Completado {{ $ticket->completed_at->diffForHumans() }}
                                    </div>
                                @endif
                            </div>

                            @if($ticket->work_evidence)
                                <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-clipboard-check text-green-600 mt-0.5 mr-2"></i>
                                        <div class="flex-1">
                                            <h5 class="text-sm font-medium text-green-800 mb-1">Evidencia de Trabajo:</h5>
                                            <p class="text-sm text-green-700">{{ Str::limit($ticket->work_evidence, 150) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col space-y-2 ml-4">
                            <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-eye mr-2"></i>
                                Ver Detalles
                            </a>
                            
                            @if(!$ticket->isFinalizado())
                                <a href="{{ route('almacen.tickets.show', $ticket) }}#complete-ticket" 
                                   class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <i class="fas fa-check mr-2"></i>
                                    Completar Ticket
                                </a>
                            @else
                                <div class="inline-flex items-center justify-center px-4 py-2 bg-green-100 text-green-800 rounded-md text-sm font-medium">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Completado
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                    <i class="fas fa-clipboard-list text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if(request('status'))
                        No tienes tickets {{ request('status') === \App\Models\Ticket::STATUS_EN_PROCESO ? 'en proceso' : 'completados' }}
                    @else
                        No tienes tickets asignados
                    @endif
                </h3>
                <p class="text-sm text-gray-500 mb-6">
                    @if(request('status'))
                        Intenta cambiar el filtro para ver otros tickets.
                    @else
                        Dirígete al dashboard para tomar tickets pendientes.
                    @endif
                </p>
                @if(!request('status'))
                    <a href="{{ route('almacen.dashboard') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Ir al Dashboard
                    </a>
                @else
                    <a href="{{ route('almacen.tickets.mine') }}" 
                       class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-list mr-2"></i>
                        Ver Todos los Tickets
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($tickets->hasPages())
        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection

