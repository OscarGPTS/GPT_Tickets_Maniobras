@extends('layouts.app')

@section('title', 'Panel de Almacén')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Panel de Almacén</h1>
                        <p class="text-gray-600 mt-2">Gestiona los tickets asignados y supervisa el flujo de trabajo</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Bienvenido</p>
                        <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Tickets Pendientes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Tickets Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Ticket::where('status', 'pendiente')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Tickets Asignados a Mí -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Mis Tickets</p>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Ticket::where('assigned_to', auth()->id())->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- En Progreso -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">En Progreso</p>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Ticket::where('status', 'en_progreso')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Completados Hoy -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Completados Hoy</p>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Ticket::where('status', 'completado')->whereDate('updated_at', today())->count() }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Grid de Contenido Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Panel Izquierdo - Tickets Pendientes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Tickets Pendientes</h3>
                        <a href="{{ route('almacen.tickets.pending') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Ver todos →
                        </a>
                    </div>
                </div>
                
                <div class="p-6">
                    @php
                        $pendingTickets = \App\Models\Ticket::where('status', 'pendiente')
                            ->with(['user'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    <div class="space-y-4">
                        @forelse($pendingTickets as $ticket)
                            <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 rounded-r-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-900">
                                        #{{ $ticket->id }} - {{ Str::limit($ticket->title, 30) }}
                                    </h4>
                                    <span class="text-xs text-gray-500">{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ Str::limit($ticket->description, 60) }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Por: {{ $ticket->user->name }}</span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                           class="text-blue-600 hover:text-blue-700 text-xs">Ver</a>
                                        <form method="POST" action="{{ route('almacen.tickets.assign', $ticket) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-700 text-xs">
                                                Asignar a mí
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">¡Excelente!</h3>
                                <p class="mt-1 text-sm text-gray-500">No hay tickets pendientes por asignar</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Panel Derecho - Mis Tickets -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Mis Tickets Asignados</h3>
                        <a href="{{ route('almacen.tickets.mine') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Ver todos →
                        </a>
                    </div>
                </div>
                
                <div class="p-6">
                    @php
                        $myTickets = \App\Models\Ticket::where('assigned_to', auth()->id())
                            ->with(['user'])
                            ->orderBy('updated_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    <div class="space-y-4">
                        @forelse($myTickets as $ticket)
                            <div class="border-l-4 
                                @if($ticket->status === 'completado') border-green-400 bg-green-50
                                @elseif($ticket->status === 'en_progreso') border-blue-400 bg-blue-50
                                @else border-gray-400 bg-gray-50 @endif 
                                p-4 rounded-r-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-900">
                                        #{{ $ticket->id }} - {{ Str::limit($ticket->title, 30) }}
                                    </h4>
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        @if($ticket->status === 'completado') bg-green-100 text-green-800
                                        @elseif($ticket->status === 'en_progreso') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ Str::limit($ticket->description, 60) }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Por: {{ $ticket->user->name }}</span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                           class="text-blue-600 hover:text-blue-700 text-xs">Ver</a>
                                        @if($ticket->status !== 'completado')
                                            <a href="{{ route('almacen.tickets.complete.form', $ticket) }}" 
                                               class="text-green-600 hover:text-green-700 text-xs">Completar</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Sin tickets asignados</h3>
                                <p class="mt-1 text-sm text-gray-500">No tienes tickets asignados actualmente</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- Acciones Rápidas -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Ver Tickets Pendientes -->
                    <a href="{{ route('almacen.tickets.pending') }}" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        <div class="w-8 h-8 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Tickets Pendientes</p>
                            <p class="text-xs text-gray-600">Asignar y gestionar</p>
                        </div>
                    </a>

                    <!-- Mis Tickets -->
                    <a href="{{ route('almacen.tickets.mine') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Mis Tickets</p>
                            <p class="text-xs text-gray-600">Ver asignados</p>
                        </div>
                    </a>

                    <!-- Estadísticas -->
                    <a href="{{ route('almacen.statistics') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Estadísticas</p>
                            <p class="text-xs text-gray-600">Ver rendimiento</p>
                        </div>
                    </a>

                    <!-- Todos los Tickets -->
                    <a href="{{ route('tickets.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Todos los Tickets</p>
                            <p class="text-xs text-gray-600">Vista general</p>
                        </div>
                    </a>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection