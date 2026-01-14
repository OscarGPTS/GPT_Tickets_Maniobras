@extends('layouts.app')

@section('title', 'Mis Encuestas')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Mis Encuestas</h1>
            <p class="mt-1 text-sm text-gray-600">
                Revisa y completa las encuestas de satisfacción de tus tickets finalizados
            </p>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-list h-6 w-6 text-gray-400"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Encuestas</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock h-6 w-6 text-yellow-400"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pendientes</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['pending'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle h-6 w-6 text-green-400"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Completadas</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['completed'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-star h-6 w-6 text-yellow-400"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Promedio</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    @if($stats['average_rating'])
                                        {{ number_format($stats['average_rating'], 1) }}/5
                                    @else
                                        Sin datos
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <label for="status-filter" class="text-sm font-medium text-gray-700">Estado:</label>
                        <select id="status-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas</option>
                            <option value="pending">Pendientes</option>
                            <option value="completed">Completadas</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label for="rating-filter" class="text-sm font-medium text-gray-700">Calificación:</label>
                        <select id="rating-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas</option>
                            <option value="5">5 estrellas</option>
                            <option value="4">4 estrellas</option>
                            <option value="3">3 estrellas</option>
                            <option value="2">2 estrellas</option>
                            <option value="1">1 estrella</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Encuestas -->
        <div class="bg-white shadow rounded-lg">
            @if($surveys->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($surveys as $survey)
                        <div class="p-6 hover:bg-gray-50 transition duration-150 ease-in-out">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                Ticket #{{ $survey->ticket->id }}: {{ $survey->ticket->title }}
                                            </h3>
                                            <div class="mt-1 flex items-center text-sm text-gray-500 space-x-4">
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Ticket completado: {{ $survey->ticket->completed_at->format('d/m/Y H:i') }}
                                                </span>
                                                @if($survey->completed_at)
                                                    <span class="flex items-center">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Encuesta completada: {{ $survey->completed_at->format('d/m/Y H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                                                {{ Str::limit($survey->ticket->description, 120) }}
                                            </p>
                                            
                                            @if($survey->ticket->assignedTo)
                                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                                    <i class="fas fa-user mr-1"></i>
                                                    Atendido por: {{ $survey->ticket->assignedTo->name }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Estado de la encuesta -->
                                        <div class="flex-shrink-0">
                                            @if($survey->completed_at)
                                                <div class="flex flex-col items-end">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Completada
                                                    </span>
                                                    @if($survey->rating)
                                                        <div class="mt-2 flex items-center">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star text-sm {{ $i <= $survey->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                            @endfor
                                                            <span class="ml-1 text-sm text-gray-600">{{ $survey->rating }}/5</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Pendiente
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Acciones -->
                                <div class="flex-shrink-0 flex items-center space-x-2 ml-4">
                                    <a href="{{ route('solicitante.surveys.show', $survey) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-eye mr-1"></i>
                                        Ver
                                    </a>
                                    
                                    @if(!$survey->completed_at)
                                        <a href="{{ route('solicitante.surveys.edit', $survey) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-yellow-600 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                            <i class="fas fa-star mr-1"></i>
                                            Calificar
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('solicitante.tickets.show', $survey->ticket) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-ticket-alt mr-1"></i>
                                        Ver Ticket
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($surveys->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $surveys->links() }}
                    </div>
                @endif
            @else
                <!-- Estado vacío -->
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400">
                        <i class="fas fa-star text-6xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tienes encuestas</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Las encuestas aparecerán aquí cuando tus tickets sean completados.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('solicitante.tickets.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent shadow-sm text-sm font-medium rounded-md text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-ticket-alt mr-2"></i>
                            Ver mis tickets
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtro por estado
    document.getElementById('status-filter').addEventListener('change', function() {
        filterSurveys();
    });

    // Filtro por calificación
    document.getElementById('rating-filter').addEventListener('change', function() {
        filterSurveys();
    });

    function filterSurveys() {
        const statusFilter = document.getElementById('status-filter').value;
        const ratingFilter = document.getElementById('rating-filter').value;
        
        const url = new URL(window.location.href);
        
        if (statusFilter) {
            url.searchParams.set('status', statusFilter);
        } else {
            url.searchParams.delete('status');
        }
        
        if (ratingFilter) {
            url.searchParams.set('rating', ratingFilter);
        } else {
            url.searchParams.delete('rating');
        }
        
        window.location.href = url.toString();
    }
});
</script>
@endpush
@endsection
