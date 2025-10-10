@extends('layouts.app')

@section('title', 'Encuestas Pendientes')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    <i class="fas fa-clock text-yellow-500 mr-2"></i>
                    Encuestas Pendientes
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Completa las encuestas de satisfacción de tus tickets finalizados
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('surveys.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-list mr-2"></i>
                    Todas las Encuestas
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle h-5 w-5 text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle h-5 w-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($pendingSurveys->count() > 0)
            <!-- Resumen -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle h-5 w-5 text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Tienes {{ $pendingSurveys->count() }} {{ $pendingSurveys->count() == 1 ? 'encuesta pendiente' : 'encuestas pendientes' }}
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Tu opinión es importante para nosotros. Completa las encuestas para ayudarnos a mejorar nuestro servicio.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de encuestas pendientes -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @foreach($pendingSurveys as $survey)
                        <li class="hover:bg-gray-50">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-star text-yellow-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4 min-w-0 flex-1">
                                            <div class="flex items-center">
                                                <h3 class="text-sm font-medium text-gray-900 truncate">
                                                    Ticket #{{ $survey->ticket->id }}: {{ $survey->ticket->title }}
                                                </h3>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Pendiente
                                                </span>
                                            </div>
                                            <div class="mt-1">
                                                <p class="text-sm text-gray-500 line-clamp-2">
                                                    {{ Str::limit($survey->ticket->description, 120) }}
                                                </p>
                                            </div>
                                            <div class="mt-2 flex items-center text-sm text-gray-500 space-x-4">
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Completado: {{ $survey->ticket->completed_at->format('d/m/Y H:i') }}
                                                </div>
                                                @if($survey->ticket->assignedTo)
                                                    <div class="flex items-center">
                                                        <i class="fas fa-user mr-1"></i>
                                                        Atendido por: {{ $survey->ticket->assignedTo->name }}
                                                    </div>
                                                @endif
                                                <div class="flex items-center">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Pendiente desde: {{ $survey->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        <!-- Calificación rápida con 5 estrellas -->
                                        <form action="{{ route('surveys.quickComplete', $survey) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('¿Calificar este servicio con 5 estrellas?')"
                                                    class="inline-flex items-center px-3 py-2 border border-yellow-300 shadow-sm text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                                                    title="Calificación rápida con 5 estrellas">
                                                <i class="fas fa-thumbs-up mr-1"></i>
                                                5★
                                            </button>
                                        </form>
                                        
                                        <!-- Completar encuesta personalizada -->
                                        <a href="{{ route('surveys.edit', $survey) }}" 
                                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-star mr-2"></i>
                                            Calificar
                                        </a>
                                        
                                        <!-- Ver detalles -->
                                        <a href="{{ route('surveys.show', $survey) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Información adicional expandible -->
                                @if($survey->ticket->work_evidence || $survey->ticket->images->count() > 0)
                                    <div class="mt-4 border-t border-gray-200 pt-4">
                                        @if($survey->ticket->work_evidence)
                                            <div class="mb-3">
                                                <h4 class="text-sm font-medium text-gray-700 mb-1">Trabajo realizado:</h4>
                                                <p class="text-sm text-gray-600 bg-green-50 p-2 rounded border-l-4 border-green-400">
                                                    {{ Str::limit($survey->ticket->work_evidence, 200) }}
                                                </p>
                                            </div>
                                        @endif
                                        
                                        @if($survey->ticket->images->count() > 0)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-700 mb-2">Evidencias del trabajo:</h4>
                                                <div class="flex space-x-2">
                                                    @foreach($survey->ticket->images->take(3) as $image)
                                                        <img src="{{ Storage::url($image->file_path) }}" 
                                                             alt="Evidencia" 
                                                             class="h-16 w-16 object-cover rounded-md border cursor-pointer hover:opacity-75"
                                                             onclick="openImageModal('{{ Storage::url($image->file_path) }}', '{{ $image->original_name }}')">
                                                    @endforeach
                                                    @if($survey->ticket->images->count() > 3)
                                                        <div class="h-16 w-16 bg-gray-100 rounded-md border flex items-center justify-center">
                                                            <span class="text-xs text-gray-500">+{{ $survey->ticket->images->count() - 3 }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Botón para completar todas las encuestas rápidamente -->
            @if($pendingSurveys->count() > 1)
                <div class="mt-6 text-center">
                    <form action="{{ route('surveys.quickCompleteAll') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('¿Calificar todos los servicios con 5 estrellas?')"
                                class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <i class="fas fa-star mr-2"></i>
                            Calificar Todas con 5 Estrellas
                        </button>
                    </form>
                </div>
            @endif
        @else
            <!-- Estado vacío -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                    <i class="fas fa-star text-6xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No tienes encuestas pendientes</h3>
                <p class="mt-2 text-sm text-gray-500">
                    ¡Excelente! Has completado todas tus encuestas de satisfacción.
                </p>
                <div class="mt-6 flex justify-center space-x-3">
                    <a href="{{ route('surveys.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-list mr-2"></i>
                        Ver Todas las Encuestas
                    </a>
                    <a href="{{ route('tickets.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-ticket-alt mr-2"></i>
                        Ver Mis Tickets
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal para ver imágenes -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900" id="modalImageTitle">Imagen</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeImageModal()">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="text-center">
            <img id="modalImage" src="" alt="Imagen ampliada" class="max-w-full max-h-96 mx-auto rounded-lg">
        </div>
    </div>
</div>

@push('scripts')
<script>
function openImageModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalImageTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endpush
@endsection