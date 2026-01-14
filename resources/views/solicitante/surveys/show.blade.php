@extends('layouts.app')

@section('title', 'Encuesta - Ticket #' . $survey->ticket->id)

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <a href="{{ route('solicitante.surveys.index') }}" 
                   class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver a Encuestas
                </a>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Encuesta de Satisfacción</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Ticket #{{ $survey->ticket->id }}: {{ $survey->ticket->title }}
                    </p>
                </div>
                @if(!$survey->completed_at)
                    <a href="{{ route('solicitante.surveys.edit', $survey) }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent shadow-sm text-sm font-medium rounded-md text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <i class="fas fa-star mr-2"></i>
                        Completar Encuesta
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Contenido principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Información del ticket -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Detalles del Ticket</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700">Descripción del trabajo solicitado</h3>
                            <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $survey->ticket->description }}</p>
                        </div>
                        
                        @if($survey->ticket->work_evidence)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700">Trabajo realizado</h3>
                                <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $survey->ticket->work_evidence }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Imágenes del ticket (si las hay) -->
                @if($survey->ticket->images->count() > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Imágenes del Trabajo</h2>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach($survey->ticket->images as $image)
                                <div class="group relative">
                                    <img src="{{ Storage::url($image->file_path) }}" 
                                         alt="Imagen del ticket" 
                                         class="h-32 w-full object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity duration-200"
                                         onclick="openImageModal('{{ Storage::url($image->file_path) }}', '{{ $image->original_name }}')">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-25 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                    </div>
                                    @if($image->uploaded_by === $survey->ticket->assigned_to)
                                        <div class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">
                                            Evidencia
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Calificación y comentarios -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Mi Calificación</h2>
                    
                    @if($survey->completed_at)
                        <!-- Encuesta completada -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Calificación del servicio</h3>
                                <div class="flex items-center space-x-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-2xl {{ $i <= $survey->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="ml-2 text-lg font-medium text-gray-900">{{ $survey->rating }}/5</span>
                                    <span class="text-sm text-gray-500">
                                        ({{ ['', 'Muy malo', 'Malo', 'Regular', 'Bueno', 'Excelente'][$survey->rating] }})
                                    </span>
                                </div>
                            </div>
                            
                            @if($survey->feedback)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 mb-2">Comentarios adicionales</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $survey->feedback }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle h-5 w-5 text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-800">
                                            <strong>Encuesta completada el {{ $survey->completed_at->format('d/m/Y H:i') }}</strong>
                                        </p>
                                        <p class="mt-1 text-sm text-green-700">
                                            Gracias por tu retroalimentación. Tu opinión nos ayuda a mejorar nuestro servicio.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Encuesta pendiente -->
                        <div class="text-center py-8">
                            <div class="mx-auto h-16 w-16 text-yellow-400 mb-4">
                                <i class="fas fa-star text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Encuesta Pendiente</h3>
                            <p class="text-sm text-gray-600 mb-6">
                                Tu ticket ha sido completado. Por favor califica la calidad del servicio recibido.
                            </p>
                            <a href="{{ route('solicitante.surveys.edit', $survey) }}" 
                               class="inline-flex items-center px-6 py-3 bg-yellow-600 border border-transparent shadow-sm text-base font-medium rounded-md text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <i class="fas fa-star mr-2"></i>
                                Completar Encuesta
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Información de la encuesta -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Información</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID del Ticket</dt>
                            <dd class="text-sm text-gray-900">#{{ $survey->ticket->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado del Ticket</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $survey->ticket->getStatusBadgeClass() }}">
                                    {{ $survey->ticket->getStatusText() }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Finalización</dt>
                            <dd class="text-sm text-gray-900">{{ $survey->ticket->completed_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        @if($survey->ticket->assignedTo)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Atendido por</dt>
                                <dd class="text-sm text-gray-900 flex items-center">
                                    @if($survey->ticket->assignedTo->avatar)
                                        <img class="h-6 w-6 rounded-full mr-2" src="{{ $survey->ticket->assignedTo->getAvatarUrl() }}" alt="">
                                    @endif
                                    {{ $survey->ticket->assignedTo->name }}
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado de la Encuesta</dt>
                            <dd>
                                @if($survey->completed_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Completada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pendiente
                                    </span>
                                @endif
                            </dd>
                        </div>
                        @if($survey->completed_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Encuesta</dt>
                                <dd class="text-sm text-gray-900">{{ $survey->completed_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Enlaces relacionados -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Enlaces Relacionados</h2>
                    <div class="space-y-3">
                        <a href="{{ route('solicitante.tickets.show', $survey->ticket) }}" 
                           class="flex items-center p-3 text-sm text-gray-700 rounded-md hover:bg-gray-50 border border-gray-200">
                            <i class="fas fa-ticket-alt mr-3 text-gray-400"></i>
                            Ver ticket completo
                        </a>
                        <a href="{{ route('solicitante.tickets.index') }}" 
                           class="flex items-center p-3 text-sm text-gray-700 rounded-md hover:bg-gray-50 border border-gray-200">
                            <i class="fas fa-list mr-3 text-gray-400"></i>
                            Todos mis tickets
                        </a>
                        @if(!$survey->completed_at)
                            <form action="{{ route('solicitante.surveys.quickComplete', $survey) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('¿Estás satisfecho con el servicio y quieres calificarlo con 5 estrellas?')"
                                        class="flex items-center w-full p-3 text-sm text-yellow-700 rounded-md hover:bg-yellow-50 border border-yellow-200">
                                    <i class="fas fa-thumbs-up mr-3 text-yellow-400"></i>
                                    Calificación rápida (5★)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
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
