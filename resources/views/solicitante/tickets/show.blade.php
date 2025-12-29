@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb y Acciones -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver a Mis Tickets
            </a>
            
            @if($ticket->status === 'pendiente')
                <div class="flex items-center space-x-3">
                    <a href="{{ route('solicitante.tickets.edit', $ticket) }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                        <i class="fas fa-edit mr-2"></i>
                        Editar
                    </a>
                    <button onclick="openCancelModal()"
                            class="inline-flex items-center px-4 py-2 bg-white border border-red-300 rounded-lg text-sm font-medium text-red-700 hover:bg-red-50 transition-colors shadow-sm">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar Ticket
                    </button>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            
            <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="text-sm font-medium text-gray-500">Ticket</span>
                        <span class="text-2xl font-bold text-gray-900">#{{ $ticket->id }}</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $ticket->title }}</h1>
                    <p class="text-gray-600 text-sm">
                        <i class="far fa-calendar mr-2"></i>
                        Creado el {{ $ticket->created_at->format('d/m/Y') }} a las {{ $ticket->created_at->format('H:i') }}
                    </p>
                </div>
            </div>

            <!-- Flowbite Stepper Horizontal -->
            <ol class="flex items-center w-full text-sm font-medium text-center text-gray-500 sm:text-base">
                <!-- Paso 1: Creado -->
                <li class="flex md:w-full items-center {{ $ticket->created_at ? 'text-blue-600' : 'text-gray-500' }} sm:after:content-[''] after:w-full after:h-1 after:border-b {{ $ticket->assigned_at ? 'after:border-blue-600' : 'after:border-gray-200' }} after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10">
                    <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200">
                        <span class="flex items-center justify-center w-8 h-8 {{ $ticket->created_at ? 'bg-blue-100' : 'bg-gray-100' }} rounded-full lg:h-10 lg:w-10 shrink-0">
                            <i class="fas fa-plus {{ $ticket->created_at ? 'text-blue-600' : 'text-gray-400' }}"></i>
                        </span>
                        <span class="ml-2">
                            <span class="block font-semibold">Creado</span>
                            @if($ticket->created_at)
                                <span class="hidden sm:block text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</span>
                            @endif
                        </span>
                    </span>
                </li>

                <!-- Paso 2: Asignado -->
                <li class="flex md:w-full items-center {{ $ticket->assigned_at ? 'text-blue-600' : 'text-gray-500' }} sm:after:content-[''] after:w-full after:h-1 after:border-b {{ $ticket->completed_at || $ticket->status === 'cancelado' ? 'after:border-blue-600' : 'after:border-gray-200' }} after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10">
                    <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200">
                        <span class="flex items-center justify-center w-8 h-8 {{ $ticket->assigned_at ? 'bg-blue-100' : 'bg-gray-100' }} rounded-full lg:h-10 lg:w-10 shrink-0">
                            <i class="fas fa-user-check {{ $ticket->assigned_at ? 'text-blue-600' : 'text-gray-400' }}"></i>
                        </span>
                        <span class="ml-2">
                            <span class="block font-semibold">{{ $ticket->assigned_at ? 'Asignado' : 'Por asignar' }}</span>
                            @if($ticket->assigned_at)
                                <span class="hidden sm:block text-xs text-gray-500">{{ $ticket->assigned_at->format('d/m/Y') }}</span>
                                <span class="hidden sm:block text-xs font-medium text-gray-600">{{ $ticket->assignedTo->name ?? '' }}</span>
                            @endif
                        </span>
                    </span>
                </li>

                <!-- Paso 3: Completado/Cancelado -->
                @if($ticket->status === 'cancelado')
                    <li class="flex md:w-full items-center text-red-600">
                        <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200">
                            <span class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-full lg:h-10 lg:w-10 shrink-0">
                                <i class="fas fa-times text-red-600"></i>
                            </span>
                            <span class="ml-2">
                                <span class="block font-semibold">Cancelado</span>
                                @if($ticket->updated_at)
                                    <span class="hidden sm:block text-xs text-gray-500">{{ $ticket->updated_at->format('d/m/Y') }}</span>
                                @endif
                            </span>
                        </span>
                    </li>
                @else
                    <li class="flex md:w-full items-center {{ $ticket->completed_at ? 'text-blue-600' : 'text-gray-500' }} sm:after:content-[''] after:w-full after:h-1 after:border-b {{ $ticket->survey && $ticket->survey->completed_at ? 'after:border-blue-600' : 'after:border-gray-200' }} after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10">
                        <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200">
                            <span class="flex items-center justify-center w-8 h-8 {{ $ticket->completed_at ? 'bg-blue-100' : 'bg-gray-100' }} rounded-full lg:h-10 lg:w-10 shrink-0">
                                <i class="fas fa-check {{ $ticket->completed_at ? 'text-blue-600' : 'text-gray-400' }}"></i>
                            </span>
                            <span class="ml-2">
                                <span class="block font-semibold">{{ $ticket->completed_at ? 'Completado' : 'En proceso' }}</span>
                                @if($ticket->completed_at)
                                    <span class="hidden sm:block text-xs text-gray-500">{{ $ticket->completed_at->format('d/m/Y') }}</span>
                                @endif
                            </span>
                        </span>
                    </li>

                    <!-- Paso 4: Calificado -->
                    <li class="flex items-center {{ $ticket->survey && $ticket->survey->completed_at ? 'text-blue-600' : 'text-gray-500' }}">
                        <span class="flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 {{ $ticket->survey && $ticket->survey->completed_at ? 'bg-blue-100' : 'bg-gray-100' }} rounded-full lg:h-10 lg:w-10 shrink-0">
                                <i class="fas fa-star {{ $ticket->survey && $ticket->survey->completed_at ? 'text-blue-600' : 'text-gray-400' }}"></i>
                            </span>
                            <span class="ml-2">
                                <span class="block font-semibold">{{ $ticket->survey && $ticket->survey->completed_at ? 'Calificado' : 'Por calificar' }}</span>
                                @if($ticket->survey && $ticket->survey->completed_at)
                                    <span class="hidden sm:block text-xs text-gray-500">{{ $ticket->survey->completed_at->format('d/m/Y') }}</span>
                                    <span class="flex items-center mt-0.5">
                                        @for($i = 1; $i <= $ticket->survey->rating; $i++)
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        @endfor
                                    </span>
                                @endif
                            </span>
                        </span>
                    </li>
                @endif
            </ol>

        </div>

        <!-- Alerta de Cancelación -->
        @if($ticket->status === 'cancelado')
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-red-900 mb-2">
                            Este ticket ha sido cancelado
                        </h3>
                        @if($ticket->cancellation_reason)
                            <div class="bg-white rounded-lg p-4 border border-red-200">
                                <p class="text-sm font-medium text-red-900 mb-1">Motivo:</p>
                                <p class="text-sm text-red-800">{{ $ticket->cancellation_reason }}</p>
                            </div>
                        @else
                            <p class="text-sm text-red-700">No se proporcionó un motivo de cancelación.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Contenido -->
        <div class="space-y-6">
                
                <!-- Información del Ticket -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info-circle text-indigo-600 text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 ml-3">Información del Ticket</h2>
                    </div>
                    
                    <!-- Descripción -->
                    <div class="mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Descripción:</p>
                            <p class="text-gray-800 whitespace-pre-line leading-relaxed">{{ $ticket->description }}</p>
                        </div>
                    </div>

                    <!-- Detalles en Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- ID -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">ID del Ticket</span>
                            <span class="text-sm font-bold text-gray-900">#{{ $ticket->id }}</span>
                        </div>
                        
                        <!-- Estado -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Estado</span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded {{ $ticket->getStatusBadgeClass() }}">
                                {{ $ticket->getStatusText() }}
                            </span>
                        </div>
                        
                        <!-- Prioridad -->
                        @if($ticket->priority)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Prioridad</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $ticket->priority === 'alta' ? 'bg-red-100 text-red-800' : ($ticket->priority === 'media' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </div>
                        @endif
                        
                        <!-- Departamento -->
                        @if($ticket->department)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Departamento</span>
                                <span class="text-sm font-medium text-gray-900">{{ $ticket->department }}</span>
                            </div>
                        @endif
                        
                        <!-- Fecha Creación -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Creado</span>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $ticket->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $ticket->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        
                        <!-- Asignado a -->
                        @if($ticket->assignedTo)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Asignado a</span>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $ticket->assignedTo->name }}</p>
                                    <p class="text-xs text-gray-500">Almacén</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Tiempo transcurrido -->
                        @if($ticket->completed_at && $ticket->assigned_at)
                            @php
                                $diff = $ticket->assigned_at->diff($ticket->completed_at);
                                $hours = ($diff->days * 24) + $diff->h;
                                $minutes = $diff->i;
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                <span class="text-sm text-gray-600">Tiempo de resolución</span>
                                <span class="text-sm font-bold text-green-600">
                                    {{ $hours }}h {{ $minutes }}m
                                </span>
                            </div>
                        @elseif($ticket->assigned_at)
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <span class="text-sm text-gray-600">En progreso desde</span>
                                <span class="text-sm font-medium text-blue-600">
                                    {{ $ticket->assigned_at->diffForHumans() }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Imágenes de la Solicitud -->
                @php
                    $solicitudImages = $ticket->images->where('type', 'solicitud');
                @endphp
                @if($solicitudImages->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-images text-blue-600 text-lg"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 ml-3">Fotos de la Solicitud</h2>
                            </div>
                            <span class="text-sm text-gray-500">{{ $solicitudImages->count() }} imágenes</span>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($solicitudImages as $image)
                                <div class="group relative cursor-pointer rounded-lg overflow-hidden aspect-square"
                                     onclick="openImageModal('{{ Storage::url($image->file_path) }}', '{{ $image->original_name }}')">
                                    <img src="{{ Storage::url($image->file_path) }}" 
                                         alt="{{ $image->original_name }}" 
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-2xl"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Evidencia del Trabajo -->
                @if($ticket->status === 'finalizado' && $ticket->work_evidence)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-check text-green-600 text-lg"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 ml-3">Trabajo Realizado</h2>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Descripción del trabajo:</p>
                            <p class="text-gray-800 whitespace-pre-line leading-relaxed">{{ $ticket->work_evidence }}</p>
                        </div>

                        <!-- Imágenes de Evidencia -->
                        @php
                            $evidenceImages = $ticket->images->where('type', 'evidencia');
                        @endphp
                        
                        @if($evidenceImages->count() > 0)
                            <div class="mt-6">
                                <p class="text-sm font-medium text-gray-700 mb-3">
                                    <i class="fas fa-camera mr-2"></i>Evidencia fotográfica ({{ $evidenceImages->count() }})
                                </p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($evidenceImages as $image)
                                        <div class="group relative cursor-pointer rounded-lg overflow-hidden aspect-square"
                                             onclick="openImageModal('{{ Storage::url($image->file_path) }}', 'Evidencia - {{ $image->original_name }}')">
                                            <img src="{{ Storage::url($image->file_path) }}" 
                                                 alt="Evidencia" 
                                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                                                <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-2xl"></i>
                                            </div>
                                            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                                <i class="fas fa-check mr-1"></i>Evidencia
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Formulario de Encuesta -->
                @if($ticket->status === 'finalizado' && $ticket->survey && !$ticket->survey->completed_at)
                    <div id="survey-form" class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl shadow-sm border-2 border-yellow-300 p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-yellow-400 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-white text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h2 class="text-xl font-bold text-gray-900">¡Tu opinión es importante!</h2>
                                <p class="text-sm text-gray-600">Califica el servicio recibido</p>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route('solicitante.surveys.complete', $ticket->survey) }}" class="space-y-5">
                            @csrf
                            
                            <!-- Rating Stars -->
                            <div class="bg-white rounded-lg p-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    ¿Cómo calificarías el servicio? *
                                </label>
                                <div class="flex justify-center space-x-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" onclick="setRating({{ $i }})" 
                                                class="rating-star text-gray-300 hover:text-yellow-400 transition-all duration-200 transform hover:scale-125">
                                            <i class="fas fa-star text-4xl"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="" required>
                                <p id="rating-text" class="text-center text-sm font-medium text-gray-600 mt-3"></p>
                            </div>
                            
                            <!-- Comments -->
                            <div class="bg-white rounded-lg p-6">
                                <label for="comments" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Comentarios adicionales (opcional)
                                </label>
                                <textarea name="comments" id="comments" rows="4" 
                                         class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all" 
                                         placeholder="Cuéntanos sobre tu experiencia..."></textarea>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:from-yellow-600 hover:to-orange-600 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Enviar Calificación
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Calificación Completada -->
                @if($ticket->survey && $ticket->survey->completed_at)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-yellow-600 text-lg"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 ml-3">Tu Calificación</h2>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $ticket->survey->rating ? 'text-yellow-400' : 'text-gray-300' }} text-xl"></i>
                                    @endfor
                                    <span class="ml-2 text-lg font-bold text-gray-900">{{ $ticket->survey->rating }}/5</span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ $ticket->survey->completed_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            @if($ticket->survey->comments)
                                <div class="mt-3 pt-3 border-t border-yellow-200">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Tu comentario:</p>
                                    <p class="text-sm text-gray-800">{{ $ticket->survey->comments }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

    </div>
</div>

<!-- Modal de Cancelación -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all" onclick="event.stopPropagation()">
        <div class="flex items-start mb-4">
            <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-xl font-bold text-gray-900 mb-1">¿Cancelar este ticket?</h3>
                <p class="text-sm text-gray-600">Esta acción no se puede deshacer.</p>
            </div>
            <button onclick="closeCancelModal()" 
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('solicitante.tickets.cancel', $ticket) }}" class="mt-4">
            @csrf
            <div class="mb-4">
                <label for="cancellation_reason_modal" class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo de cancelación (opcional)
                </label>
                <textarea name="cancellation_reason" id="cancellation_reason_modal" rows="3" 
                         class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                         placeholder="Explica por qué cancelas este ticket..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" 
                        onclick="closeCancelModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    No, volver
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                    Sí, cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Imagen -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 items-center justify-center p-4" style="display: none;">
    <div class="relative max-w-4xl max-h-full" onclick="event.stopPropagation()">
        <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[80vh] rounded-lg shadow-2xl">
        <p id="modalImageTitle" class="text-white text-center mt-4 text-sm"></p>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Rating System
function setRating(rating) {
    document.getElementById('rating-input').value = rating;
    const stars = document.querySelectorAll('.rating-star i');
    const texts = ['Muy malo', 'Malo', 'Regular', 'Bueno', 'Excelente'];
    
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.add('text-gray-300');
            star.classList.remove('text-yellow-400');
        }
    });
    
    document.getElementById('rating-text').textContent = texts[rating - 1];
}

// Cancel Modal
function openCancelModal() {
    const modal = document.getElementById('cancelModal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}

function closeCancelModal() {
    const modal = document.getElementById('cancelModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

// Image Modal
function openImageModal(src, title) {
    const modal = document.getElementById('imageModal');
    document.getElementById('modalImage').src = src;
    document.getElementById('modalImageTitle').textContent = title;
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

// Cerrar modales al hacer clic en el fondo
document.getElementById('cancelModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});

document.getElementById('imageModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Cerrar modales con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const imageModal = document.getElementById('imageModal');
        const cancelModal = document.getElementById('cancelModal');
        
        if (imageModal && !imageModal.classList.contains('hidden')) {
            closeImageModal();
        }
        if (cancelModal && !cancelModal.classList.contains('hidden')) {
            closeCancelModal();
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.rating-star {
    cursor: pointer;
    transition: all 0.2s ease;
}

.rating-star:hover {
    transform: scale(1.2);
}

#imageModal img {
    animation: fadeInScale 0.3s ease;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
@endpush
