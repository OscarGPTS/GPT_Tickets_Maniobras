@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <a href="{{ route('tickets.index') }}" 
                   class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver a Mis Tickets
                </a>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->title }}</h1>
                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-calendar mr-1"></i>
                            Creado el {{ $ticket->created_at->format('d/m/Y H:i') }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getStatusBadgeClass() }}">
                            {{ $ticket->getStatusText() }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @if($ticket->status === 'pendiente')
                        <a href="{{ route('tickets.edit', $ticket) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-edit mr-1"></i>
                            Editar
                        </a>
                    @endif
                    
               
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Contenido principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Descripción -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Descripción</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-line">{{ $ticket->description }}</p>
                    </div>
                </div>

                <!-- Imágenes adjuntas -->
                @if($ticket->images->count() > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            Imágenes Adjuntas ({{ $ticket->images->count() }})
                        </h2>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach($ticket->images as $image)
                                <div class="group relative">
                                    <img src="{{ Storage::url($image->file_path) }}" 
                                         alt="Imagen del ticket" 
                                         class="h-32 w-full object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity duration-200"
                                         onclick="openImageModal('{{ Storage::url($image->file_path) }}', '{{ $image->original_name }}')">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-25 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                    </div>
                                    <div class="absolute bottom-1 left-1 bg-black bg-opacity-75 text-white text-xs px-1 rounded max-w-full truncate">
                                        {{ $image->original_name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Evidencia de trabajo (solo si está completado) -->
                @if($ticket->status === 'finalizado' && $ticket->work_evidence)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Evidencia del Trabajo Realizado</h2>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-line">{{ $ticket->work_evidence }}</p>
                        </div>
                        
                        <!-- Imágenes de evidencia -->
                        @php
                            $evidenceImages = $ticket->images()->where('uploaded_by', $ticket->assigned_to)->get();
                        @endphp
                        
                        @if($evidenceImages->count() > 0)
                            <div class="mt-4">
                                <h3 class="text-md font-medium text-gray-900 mb-2">Imágenes de Evidencia</h3>
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                                    @foreach($evidenceImages as $image)
                                        <div class="group relative">
                                            <img src="{{ Storage::url($image->file_path) }}" 
                                                 alt="Evidencia del trabajo" 
                                                 class="h-32 w-full object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity duration-200"
                                                 onclick="openImageModal('{{ Storage::url($image->file_path) }}', 'Evidencia - {{ $image->original_name }}')">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-25 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Formulario de Encuesta Simplificado -->
                @if($ticket->status === 'finalizado' && $ticket->survey && !$ticket->survey->completed_at)
                    <div id="survey-form" class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Califica tu Experiencia</h2>
                        <form method="POST" action="{{ route('surveys.complete', $ticket->survey) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="text-sm font-medium text-gray-700">Calificación (1-5 estrellas):</label>
                                <div class="flex space-x-1 mt-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" onclick="setRating({{ $i }})" class="rating-star text-gray-300 hover:text-yellow-400 transition-colors">
                                            <i class="fas fa-star text-xl"></i>
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" id="rating-input" value="" required>
                                </div>
                                <span id="rating-text" class="text-sm text-gray-500 mt-1 block"></span>
                            </div>
                            
                            <div>
                                <label for="comments" class="text-sm font-medium text-gray-700">Comentarios (opcional):</label>
                                <textarea name="comments" id="comments" rows="3" 
                                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                         placeholder="Comparte tu experiencia..."></textarea>
                            </div>
                            
                            <div>
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                    Enviar Calificación
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Calificación (si existe) -->
                @if($ticket->survey && $ticket->survey->completed_at)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Tu Calificación</h2>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $ticket->survey->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">{{ $ticket->survey->rating }}/5</span>
                            </div>
                            <span class="text-sm text-gray-500">
                                Calificado el {{ $ticket->survey->completed_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        @if($ticket->survey->feedback)
                            <div class="mt-3">
                                <p class="text-sm text-gray-700">{{ $ticket->survey->feedback }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Información del ticket -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Información</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID del Ticket</dt>
                            <dd class="text-sm text-gray-900">#{{ $ticket->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getStatusBadgeClass() }}">
                                    {{ $ticket->getStatusText() }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Creación</dt>
                            <dd class="text-sm text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        @if($ticket->assigned_to)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Asignado a</dt>
                                <dd class="text-sm text-gray-900 flex items-center">
                                    @if($ticket->assignedTo->avatar)
                                        <img class="h-6 w-6 rounded-full mr-2" src="{{ $ticket->assignedTo->getAvatarUrl() }}" alt="">
                                    @endif
                                    {{ $ticket->assignedTo->name }}
                                </dd>
                            </div>
                        @endif
                        @if($ticket->assigned_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Asignación</dt>
                                <dd class="text-sm text-gray-900">{{ $ticket->assigned_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                        @if($ticket->completed_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Finalización</dt>
                                <dd class="text-sm text-gray-900">{{ $ticket->completed_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Timeline del ticket -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Historial</h2>
                    <div class="flow-root">
                        <ul class="mb-4">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-plus h-3 w-3 text-white"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Ticket creado</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $ticket->created_at->format('d/m H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($ticket->assigned_at)
                                <li>
                                    <div class="relative pb-8">
                                        @if($ticket->completed_at)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-user h-3 w-3 text-white"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Asignado a {{ $ticket->assignedTo->name }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $ticket->assigned_at->format('d/m H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            
                            @if($ticket->completed_at)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-check h-3 w-3 text-white"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Trabajo completado</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $ticket->completed_at->format('d/m H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
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

// Función para calificación con estrellas
function setRating(rating) {
    const ratingInput = document.getElementById('rating-input');
    const ratingText = document.getElementById('rating-text');
    const stars = document.querySelectorAll('.rating-star');
    
    // Actualizar el input hidden
    ratingInput.value = rating;
    
    // Actualizar las estrellas visualmente
    stars.forEach((star, index) => {
        const icon = star.querySelector('i');
        if (index < rating) {
            icon.classList.remove('text-gray-300');
            icon.classList.add('text-yellow-400');
        } else {
            icon.classList.remove('text-yellow-400');
            icon.classList.add('text-gray-300');
        }
    });
    
    // Actualizar el texto de la calificación
    const ratingLabels = ['', 'Muy malo', 'Malo', 'Regular', 'Bueno', 'Excelente'];
    ratingText.textContent = ratingLabels[rating];
}
</script>
@endpush
@endsection