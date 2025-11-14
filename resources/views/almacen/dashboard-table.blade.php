@extends('layouts.app')

@section('title', 'Panel de Almacén')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Panel de Almacén</h1>
            <p class="mt-1 text-sm text-gray-600">
                Gestiona y responde a las solicitudes de servicio
            </p>
        </div>

        <!-- Métricas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Pendientes -->
            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium uppercase tracking-wide">Pendientes</p>
                        <p class="text-4xl font-bold mt-2">{{ $pendingTickets->total() }}</p>
                        <p class="text-yellow-100 text-sm mt-1">Esperando asignación</p>
                    </div>
                    <div class="bg-yellow-500 bg-opacity-30 rounded-full p-4">
                        <i class="fas fa-clock text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- En proceso -->
            <div class="bg-gradient-to-br from-blue-400 to-indigo-600 rounded-lg shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">En Proceso</p>
                        <p class="text-4xl font-bold mt-2">{{ $myTickets->total() }}</p>
                        <p class="text-blue-100 text-sm mt-1">Asignados a ti</p>
                    </div>
                    <div class="bg-blue-500 bg-opacity-30 rounded-full p-4">
                        <i class="fas fa-tasks text-3xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('almacen.tickets.mine') }}" class="text-white text-sm font-medium hover:text-blue-100 flex items-center">
                        Ver historial completo <i class="fas fa-arrow-right ml-2"></i>
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

        <!-- Tickets Pendientes - TABLA -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <span class="bg-yellow-500 text-white rounded-full p-2 mr-3">
                                <i class="fas fa-inbox"></i>
                            </span>
                            Tickets Pendientes
                            <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-600 text-white">
                                {{ $pendingTickets->total() }}
                            </span>
                        </h2>
                        <a href="{{ route('almacen.tickets.pending') }}" 
                           class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            Ver todos →
                        </a>
                    </div>
                </div>

                @if($pendingTickets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingTickets as $ticket)
                                    <tr class="hover:bg-yellow-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                                    #{{ $ticket->id }}
                                                </span>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->title, 40) }}</div>
                                                    @if($ticket->solicitudImages->count() > 0)
                                                        <div class="text-xs text-gray-500">
                                                            <i class="fas fa-camera mr-1"></i>{{ $ticket->solicitudImages->count() }} foto(s)
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $ticket->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                <div class="flex items-center mb-1">
                                                    <i class="fas fa-map-marker-alt text-purple-500 mr-2 w-4"></i>
                                                    <span class="font-medium">{{ $ticket->origin }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-map-marker-alt text-red-500 mr-2 w-4"></i>
                                                    <span class="font-medium">{{ $ticket->destination }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $ticket->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $ticket->created_at->format('H:i') }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <i class="far fa-clock mr-1"></i>{{ $ticket->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 text-xs font-medium rounded hover:bg-blue-200 transition-colors">
                                                    <i class="fas fa-eye mr-1"></i> Ver
                                                </a>
                                                <form method="POST" action="{{ route('almacen.tickets.assign', $ticket) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors">
                                                        <i class="fas fa-check mr-1"></i> Asignar a mí
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($pendingTickets->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            {{ $pendingTickets->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">¡Todo al día!</h3>
                        <p class="text-gray-600">No hay tickets pendientes por asignar en este momento.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Mis Tickets en Proceso - TABLA -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full p-2 mr-3">
                                <i class="fas fa-tasks"></i>
                            </span>
                            Mis Tickets en Proceso
                            <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-600 text-white">
                                {{ $myTickets->total() }}
                            </span>
                        </h2>
                        <a href="{{ route('almacen.tickets.mine') }}" 
                           class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            Ver historial completo →
                        </a>
                    </div>
                </div>

                @if($myTickets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($myTickets as $ticket)
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                    #{{ $ticket->id }}
                                                </span>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->title, 40) }}</div>
                                                    <div class="text-xs text-gray-500">{{ Str::limit($ticket->description, 50) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                En Progreso
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $ticket->assigned_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $ticket->assigned_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-cog mr-1"></i> Trabajar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($myTickets->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            {{ $myTickets->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                            <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Sin tickets asignados</h3>
                        <p class="text-gray-600">Asígnate tickets pendientes para comenzar a trabajar.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
