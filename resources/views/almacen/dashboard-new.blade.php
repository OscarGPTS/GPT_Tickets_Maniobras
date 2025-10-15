@extends('layouts.app')

@section('title', 'Panel de Almacén')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Panel de Almacén</h1>
        <p class="mt-1 text-sm text-gray-500">
            Gestiona y responde a las solicitudes de servicio
        </p>
    </div>

    <!-- Métricas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Pendientes -->
        <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium uppercase tracking-wide">Pendientes</p>
                    <p class="text-4xl font-bold mt-2">{{ $pendingTickets->count() }}</p>
                    <p class="text-yellow-100 text-sm mt-1">Esperando asignación</p>
                </div>
                <div class="bg-yellow-500 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-clock text-3xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="#pendingTickets" class="text-white text-sm font-medium hover:text-yellow-100 flex items-center">
                    Ver todos <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- En proceso -->
        <div class="bg-gradient-to-br from-blue-400 to-indigo-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">En Proceso</p>
                    <p class="text-4xl font-bold mt-2">{{ $myTickets->count() }}</p>
                    <p class="text-blue-100 text-sm mt-1">Asignados a ti</p>
                </div>
                <div class="bg-blue-500 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-tasks text-3xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="#myTickets" class="text-white text-sm font-medium hover:text-blue-100 flex items-center">
                    Ver mis tickets <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Completados hoy -->
        <div class="bg-gradient-to-br from-green-400 to-emerald-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Completados Hoy</p>
                    <p class="text-4xl font-bold mt-2">{{ $completedToday }}</p>
                    <p class="text-green-100 text-sm mt-1">¡Buen trabajo!</p>
                </div>
                <div class="bg-green-500 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Pendientes -->
    <div id="pendingTickets" class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <span class="bg-yellow-100 text-yellow-800 rounded-full p-2 mr-3">
                    <i class="fas fa-inbox"></i>
                </span>
                Tickets Pendientes
                <span class="ml-3 text-sm font-normal text-gray-500">({{ $pendingTickets->count() }})</span>
            </h2>
        </div>

        @if($pendingTickets->count() > 0)
            <div class="grid gap-4">
                @foreach($pendingTickets as $ticket)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-yellow-400 hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 uppercase">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            Nuevo
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            <i class="far fa-clock mr-1"></i>
                                            {{ $ticket->created_at->diffForHumans() }}
                                        </span>
                                        @if($ticket->solicitudImages->count() > 0)
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-camera mr-1"></i>
                                                {{ $ticket->solicitudImages->count() }} fotos
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
                                        <span class="font-medium">{{ $ticket->user->name }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col space-y-2 ml-4">
                                    <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-eye mr-2"></i>
                                        Ver Detalles
                                    </a>
                                    <form method="POST" action="{{ route('almacen.tickets.assign', $ticket) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-600 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <i class="fas fa-hand-pointer mr-2"></i>
                                            Tomar Ticket
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                <div class="text-gray-400 mb-3">
                    <i class="fas fa-check-double text-5xl"></i>
                </div>
                <p class="text-gray-600">¡No hay tickets pendientes! Todo está al día.</p>
            </div>
        @endif
    </div>

    <!-- Mis Tickets -->
    <div id="myTickets" class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <span class="bg-blue-100 text-blue-800 rounded-full p-2 mr-3">
                    <i class="fas fa-user-check"></i>
                </span>
                Mis Tickets en Proceso
                <span class="ml-3 text-sm font-normal text-gray-500">({{ $myTickets->count() }})</span>
            </h2>
            <a href="{{ route('almacen.tickets.mine') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                Ver todos <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        @if($myTickets->count() > 0)
            <div class="grid gap-4">
                @foreach($myTickets as $ticket)
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
                                        <i class="fas fa-tools mr-2"></i>
                                        Trabajar en Ticket
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                <div class="text-gray-400 mb-3">
                    <i class="fas fa-clipboard-list text-5xl"></i>
                </div>
                <p class="text-gray-600">No tienes tickets asignados en este momento.</p>
                <p class="text-sm text-gray-500 mt-2">Toma un ticket pendiente para comenzar a trabajar.</p>
            </div>
        @endif
    </div>
</div>
@endsection
