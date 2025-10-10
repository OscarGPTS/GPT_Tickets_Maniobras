@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                ¡Bienvenido, {{ auth()->user()->name }}!
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Gestiona tus solicitudes de movimiento de carga
            </p>
        </div>
        @if($canCreateTicket)
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <a href="{{ route('tickets.create') }}" 
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <i class="fas fa-plus -ml-0.5 mr-1.5 h-4 w-4"></i>
                    Nueva Solicitud
                </a>
            </div>
        @else
            <div class="mt-4 md:ml-4 md:mt-0">
                <div class="inline-flex items-center rounded-md bg-yellow-100 px-3 py-2 text-sm font-medium text-yellow-800">
                    <i class="fas fa-exclamation-triangle -ml-0.5 mr-1.5 h-4 w-4"></i>
                    Completa las encuestas pendientes para crear nuevas solicitudes
                </div>
            </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Tickets -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-indigo-500">
                        <i class="fas fa-ticket-alt text-white"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Total de Tickets</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_tickets'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Pending Tickets -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-yellow-500">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Pendientes</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_tickets'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- In Progress Tickets -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500">
                        <i class="fas fa-cog text-white"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">En Proceso</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['in_progress_tickets'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Completed Tickets -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500">
                        <i class="fas fa-check text-white"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Completados</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['completed_tickets'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Surveys Alert -->
    @if($pendingSurveys->count() > 0)
        <div class="rounded-md bg-yellow-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle h-5 w-5 text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Tienes {{ $pendingSurveys->count() }} encuesta(s) de satisfacción pendiente(s)
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Debes completar estas encuestas antes de poder crear nuevas solicitudes.</p>
                    </div>
                    <div class="mt-4">
                        <div class="-mx-2 -my-1.5 flex">
                            <a href="{{ route('tickets.index', ['filter' => 'survey_pending']) }}" 
                               class="rounded-md bg-yellow-50 px-2 py-1.5 text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-600 focus:ring-offset-2 focus:ring-offset-yellow-50">
                                Completar Encuestas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Tickets -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Mis Tickets Recientes</h3>
            <div class="mt-6">
                @if($myTickets->count() > 0)
                    <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-gray-200">
                            @foreach($myTickets as $ticket)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full {{ 
                                                $ticket->status === 'pendiente' ? 'bg-yellow-100' : 
                                                ($ticket->status === 'en_proceso' ? 'bg-blue-100' : 'bg-green-100') 
                                            }} flex items-center justify-center">
                                                <i class="fas {{ 
                                                    $ticket->status === 'pendiente' ? 'fa-clock text-yellow-600' : 
                                                    ($ticket->status === 'en_proceso' ? 'fa-cog text-blue-600' : 'fa-check text-green-600') 
                                                }}"></i>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-gray-900">
                                                {{ $ticket->title }}
                                            </p>
                                            <p class="truncate text-sm text-gray-500">
                                                Creado: {{ $ticket->created_at->format('d/m/Y H:i') }}
                                                @if($ticket->assignedTo)
                                                    • Asignado a: {{ $ticket->assignedTo->name }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex flex-col items-end space-y-1">
                                            {!! $ticket->status_badge !!}
                                            @if($ticket->survey && $ticket->survey->isPending())
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                                                    Encuesta Pendiente
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('tickets.show', $ticket) }}" 
                                               class="inline-flex items-center rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                                Ver
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('tickets.index') }}" 
                           class="flex w-full items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-offset-0">
                            Ver todos mis tickets
                        </a>
                    </div>
                @else
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-ticket-alt text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No tienes tickets</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza creando tu primera solicitud de movimiento de carga.</p>
                        @if($canCreateTicket)
                            <div class="mt-6">
                                <a href="{{ route('tickets.create') }}" 
                                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    <i class="fas fa-plus -ml-0.5 mr-1.5 h-4 w-4"></i>
                                    Nueva Solicitud
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection