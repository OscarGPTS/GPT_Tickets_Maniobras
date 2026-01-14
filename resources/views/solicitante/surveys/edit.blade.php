@extends('layouts.app')

@section('title', 'Completar Encuesta - Ticket #' . $survey->ticket->id)

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <a href="{{ route('solicitante.surveys.show', $survey) }}" 
                   class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver
                </a>
            </div>
            <div class="mt-2">
                <h1 class="text-2xl font-bold text-gray-900">Califica tu experiencia</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Ticket #{{ $survey->ticket->id }}: {{ $survey->ticket->title }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario principal -->
            <div class="lg:col-span-2">
                <form action="{{ route('solicitante.surveys.update', $survey) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Resumen del trabajo -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Resumen del Trabajo Realizado</h2>
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700">Trabajo solicitado</h3>
                                <p class="mt-1 text-sm text-gray-600 bg-gray-50 p-3 rounded-md">{{ $survey->ticket->description }}</p>
                            </div>
                            
                            @if($survey->ticket->work_evidence)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700">Trabajo completado</h3>
                                    <p class="mt-1 text-sm text-gray-600 bg-green-50 p-3 rounded-md border border-green-200">{{ $survey->ticket->work_evidence }}</p>
                                </div>
                            @endif
                            
                            @if($survey->ticket->assignedTo)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700">Atendido por</h3>
                                    <div class="mt-1 flex items-center">
                                        @if($survey->ticket->assignedTo->avatar)
                                            <img class="h-8 w-8 rounded-full mr-3" src="{{ $survey->ticket->assignedTo->getAvatarUrl() }}" alt="">
                                        @endif
                                        <span class="text-sm text-gray-900 font-medium">{{ $survey->ticket->assignedTo->name }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Calificación -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Tu Calificación</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    ¿Qué tan satisfecho estás con el servicio recibido? *
                                </label>
                                <div class="flex items-center justify-center space-x-2 py-4">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer" for="rating_{{ $i }}">
                                            <input type="radio" 
                                                   id="rating_{{ $i }}" 
                                                   name="rating" 
                                                   value="{{ $i }}" 
                                                   class="sr-only" 
                                                   {{ old('rating', $survey->rating) == $i ? 'checked' : '' }}
                                                   required>
                                            <i class="fas fa-star text-4xl transition-colors duration-200 hover:text-yellow-400 rating-star" 
                                               data-rating="{{ $i }}"></i>
                                        </label>
                                    @endfor
                                </div>
                                <div class="text-center">
                                    <span id="ratingText" class="text-sm text-gray-600 font-medium"></span>
                                </div>
                                @error('rating')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Comentarios adicionales -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Comentarios Adicionales</h2>
                        <div>
                            <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                                ¿Hay algo específico que te gustaría comentar sobre el servicio? (Opcional)
                            </label>
                            <textarea id="feedback" 
                                      name="feedback" 
                                      rows="4" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Comparte tus comentarios, sugerencias o cualquier detalle que consideres importante...">{{ old('feedback', $survey->feedback) }}</textarea>
                            @error('feedback')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('solicitante.surveys.show', $survey) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </a>
                            <div class="flex space-x-3">
                                <button type="button" 
                                        onclick="quickRating(5)"
                                        class="inline-flex items-center px-4 py-2 border border-yellow-300 shadow-sm text-base font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    <i class="fas fa-thumbs-up mr-2"></i>
                                    5 Estrellas
                                </button>
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-check mr-2"></i>
                                    Enviar Calificación
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar con información -->
            <div class="space-y-6">
                <!-- Información del ticket -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Detalles del Ticket</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID del Ticket</dt>
                            <dd class="text-sm text-gray-900">#{{ $survey->ticket->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
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
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tiempo de Resolución</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $survey->ticket->created_at->diffForHumans($survey->ticket->completed_at, true, false, 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Guía de calificación -->
                <div class="bg-blue-50 shadow rounded-lg p-6 border border-blue-200">
                    <h2 class="text-lg font-medium text-blue-900 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Guía de Calificación
                    </h2>
                    <div class="space-y-3 text-sm text-blue-800">
                        <div class="flex items-center">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                            </div>
                            <span><strong>1 estrella:</strong> Muy insatisfecho</span>
                        </div>
                        <div class="flex items-center">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span><strong>2 estrellas:</strong> Insatisfecho</span>
                        </div>
                        <div class="flex items-center">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span><strong>3 estrellas:</strong> Neutral</span>
                        </div>
                        <div class="flex items-center">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span><strong>4 estrellas:</strong> Satisfecho</span>
                        </div>
                        <div class="flex items-center">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span><strong>5 estrellas:</strong> Muy satisfecho</span>
                        </div>
                    </div>
                </div>

                <!-- Imágenes del trabajo (si las hay) -->
                @if($survey->ticket->images->count() > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Evidencias del Trabajo</h2>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($survey->ticket->images->take(4) as $image)
                                <img src="{{ Storage::url($image->file_path) }}" 
                                     alt="Evidencia" 
                                     class="h-20 w-full object-cover rounded-md cursor-pointer hover:opacity-75"
                                     onclick="openImageModal('{{ Storage::url($image->file_path) }}', '{{ $image->original_name }}')">
                            @endforeach
                        </div>
                        @if($survey->ticket->images->count() > 4)
                            <p class="text-xs text-gray-500 mt-2 text-center">
                                +{{ $survey->ticket->images->count() - 4 }} imágenes más
                            </p>
                        @endif
                    </div>
                @endif
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
// Variables para manejar las estrellas
const ratingTexts = {
    1: 'Muy insatisfecho',
    2: 'Insatisfecho', 
    3: 'Neutral',
    4: 'Satisfecho',
    5: 'Muy satisfecho'
};

// Inicializar estrellas
document.addEventListener('DOMContentLoaded', function() {
    updateStars();
    
    // Event listeners para las estrellas
    document.querySelectorAll('.rating-star').forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            document.querySelector(`input[value="${rating}"]`).checked = true;
            updateStars();
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = this.getAttribute('data-rating');
            highlightStars(rating);
        });
    });
    
    // Restaurar estrellas al salir del hover
    document.querySelector('.rating-star').parentElement.parentElement.addEventListener('mouseleave', function() {
        updateStars();
    });
});

function updateStars() {
    const selectedRating = document.querySelector('input[name="rating"]:checked');
    const rating = selectedRating ? selectedRating.value : 0;
    
    document.querySelectorAll('.rating-star').forEach((star, index) => {
        const starRating = index + 1;
        if (starRating <= rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
    
    // Actualizar texto
    const ratingTextElement = document.getElementById('ratingText');
    if (rating > 0) {
        ratingTextElement.textContent = ratingTexts[rating];
    } else {
        ratingTextElement.textContent = 'Selecciona una calificación';
    }
}

function highlightStars(rating) {
    document.querySelectorAll('.rating-star').forEach((star, index) => {
        const starRating = index + 1;
        if (starRating <= rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
    
    document.getElementById('ratingText').textContent = ratingTexts[rating];
}

function quickRating(rating) {
    document.querySelector(`input[value="${rating}"]`).checked = true;
    updateStars();
    
    // Scroll hacia el botón de enviar
    document.querySelector('button[type="submit"]').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
}

// Funciones para el modal de imágenes
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
