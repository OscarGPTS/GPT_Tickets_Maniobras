@extends('layouts.app')

@section('title', 'Detalle del Ticket #' . $ticket->id)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Ticket #{{ $ticket->id }}</h1>
                        <p class="text-gray-600 mt-2">{{ $ticket->title }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('almacen.tickets.pending') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Pendientes
                        </a>
                        @if($ticket->assigned_to === auth()->id())
                            <a href="{{ route('almacen.tickets.mine') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Mis Tickets
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Información Principal del Ticket -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Detalles del Ticket -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Información del Ticket</h3>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    @if($ticket->status === 'completado') bg-green-100 text-green-800
                                    @elseif($ticket->status === 'en_progreso') bg-blue-100 text-blue-800
                                    @elseif($ticket->status === 'pendiente') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                                @if($ticket->priority === 'alta')
                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                        Prioridad Alta
                                    </span>
                                @elseif($ticket->priority === 'media')
                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">
                                        Prioridad Media
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Prioridad Baja
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Título</h4>
                                <p class="text-gray-700">{{ $ticket->title }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Descripción</h4>
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->description }}</p>
                            </div>

                            @if($ticket->work_evidence)
                                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <h4 class="text-sm font-medium text-green-900 mb-2">Evidencia de Trabajo Realizado</h4>
                                    <p class="text-green-800 whitespace-pre-wrap">{{ $ticket->work_evidence }}</p>
                                    @if($ticket->completed_at)
                                        <p class="text-xs text-green-600 mt-2">Completado el {{ $ticket->completed_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Imágenes de Solicitud -->
                @if($ticket->images->where('type', 'solicitud')->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Imágenes de la Solicitud</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($ticket->images->where('type', 'solicitud') as $image)
                                    <div class="relative group">
                                        <img src="{{ Storage::url($image->file_path) }}" 
                                             alt="Imagen de solicitud" 
                                             class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="openImageModal('{{ Storage::url($image->file_path) }}', '{{ $image->original_name }}')">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity rounded-lg"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Historial de Progreso -->
                @php
                    $progressEntries = $ticket->images()->progreso()->with('uploadedBy')->orderBy('created_at', 'desc')->get();
                @endphp
                @if($progressEntries->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Historial de Progreso</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($progressEntries as $progress)
                                    <div class="border-l-4 border-blue-400 bg-blue-50 p-4 rounded-r-lg">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium text-blue-900">{{ $progress->uploadedBy->name }}</span>
                                                <span class="text-xs text-blue-600 ml-2">{{ $progress->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                        
                                        @if($progress->description)
                                            <p class="text-sm text-blue-800 mb-2">{{ $progress->description }}</p>
                                        @endif
                                        
                                        @if($progress->file_path)
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($progress->file_path) }}" 
                                                     alt="Imagen de progreso" 
                                                     class="h-24 w-24 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
                                                     onclick="openImageModal('{{ Storage::url($progress->file_path) }}', '{{ $progress->original_name }}')">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Imágenes de Evidencia -->
                @if($ticket->images->where('type', 'evidencia')->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Evidencia del Trabajo Realizado</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($ticket->images->where('type', 'evidencia') as $image)
                                    <div class="relative group">
                                        <img src="{{ Storage::url($image->file_path) }}" 
                                             alt="Evidencia de trabajo" 
                                             class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="openImageModal('{{ Storage::url($image->file_path) }}', '{{ $image->original_name }}')">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity rounded-lg"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Comentarios y Progreso -->
                @if($ticket->assigned_to === auth()->id() && $ticket->status !== 'completado')
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Agregar Progreso</h3>
                            <p class="text-sm text-gray-600 mt-1">Documenta el avance del trabajo o agrega comentarios</p>
                        </div>
                        
                        <form method="POST" action="{{ route('almacen.tickets.add-progress', $ticket) }}" enctype="multipart/form-data" class="p-6">
                            @csrf
                            
                            <!-- Comentario/Progreso -->
                            <div class="mb-4">
                                <label for="progress_comment" class="block text-sm font-medium text-gray-700 mb-2">
                                    Comentario o Progreso
                                </label>
                                <textarea name="progress_comment" 
                                          id="progress_comment" 
                                          rows="3"
                                          placeholder="Describe el progreso realizado, dificultades encontradas, o próximos pasos..."
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>

                            <!-- Imágenes de Progreso -->
                            <div class="mb-4">
                                <label for="progress_images" class="block text-sm font-medium text-gray-700 mb-2">
                                    Imágenes de Progreso (Opcional)
                                </label>
                                <div class="mt-1">
                                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors duration-200">
                                        <div class="space-y-1 text-center">
                                            <div class="mx-auto h-12 w-12 text-gray-400">
                                                <svg class="w-12 h-12" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="progress_images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    <span>Agregar imágenes</span>
                                                    <input id="progress_images" 
                                                           name="progress_images[]" 
                                                           type="file" 
                                                           class="sr-only" 
                                                           multiple 
                                                           accept="image/*"
                                                           onchange="handleProgressFileSelect(this)">
                                                </label>
                                                <p class="pl-1">o arrastra y suelta</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PNG, JPG, JPEG hasta 2MB cada una (máx. 3)
                                            </p>
                                            <p class="text-xs text-blue-600 font-medium mt-1">
                                                💡 Las imágenes se acumulan - puedes agregar más sin perder las anteriores
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Indicador de límite de imágenes -->
                                <div id="progress-image-counter" class="mt-2 hidden">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Imágenes seleccionadas:</span>
                                        <span id="progress-counter-text" class="font-medium text-gray-900">0 / 3</span>
                                    </div>
                                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                        <div id="progress-counter-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="progress-counter-message" class="mt-1 text-xs text-gray-500">Puedes subir hasta 3 imágenes</p>
                                </div>
                                
                                <!-- Vista previa de imágenes -->
                                <div id="progress-image-preview" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 hidden">
                                    <!-- Las imágenes se mostrarán aquí -->
                                </div>
                            </div>

                            <!-- Estado del Ticket -->
                            <div class="mb-4">
                                <label for="ticket_status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estado del Ticket
                                </label>
                                <select name="ticket_status" id="ticket_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="en_progreso" {{ $ticket->status === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="esperando_recursos">Esperando Recursos</option>
                                    <option value="esperando_aprobacion">Esperando Aprobación</option>
                                    <option value="completado">Marcar como Completado</option>
                                </select>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Agregar Progreso
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

            </div>

            <!-- Panel Lateral -->
            <div class="space-y-6">
                
                <!-- Información del Usuario -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Solicitante</h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-lg font-bold">{{ substr($ticket->user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $ticket->user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Ticket -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Detalles</h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Creado</p>
                            <p class="text-sm text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-500">{{ $ticket->created_at->diffForHumans() }}</p>
                        </div>

                        @if($ticket->assigned_to)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Asignado a</p>
                                <p class="text-sm text-gray-900">{{ $ticket->assignedTo->name }}</p>
                                @if($ticket->assigned_at)
                                    <p class="text-xs text-gray-500">{{ $ticket->assigned_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        @endif

                        @if($ticket->completed_at)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Completado</p>
                                <p class="text-sm text-gray-900">{{ $ticket->completed_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-500">{{ $ticket->completed_at->diffForHumans() }}</p>
                            </div>
                        @endif

                        <div>
                            <p class="text-sm font-medium text-gray-500">Prioridad</p>
                            <p class="text-sm text-gray-900">{{ ucfirst($ticket->priority) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Acciones</h3>
                    </div>
                    
                    <div class="p-6 space-y-3">
                        @if($ticket->status === 'pendiente')
                            <form method="POST" action="{{ route('almacen.tickets.assign', $ticket) }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Asignar a Mí
                                </button>
                            </form>
                        @elseif($ticket->assigned_to === auth()->id() && $ticket->status !== 'completado')
                            <a href="{{ route('almacen.tickets.complete.form', $ticket) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Completar Ticket
                            </a>
                        @elseif($ticket->status === 'completado')
                            <div class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-100 text-green-800 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Ticket Completado
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Encuesta de Satisfacción -->
                @if($ticket->survey)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Encuesta de Satisfacción</h3>
                        </div>
                        
                        <div class="p-6">
                            @if($ticket->survey->completed_at)
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Calificación</p>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $ticket->survey->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-600">({{ $ticket->survey->rating }}/5)</span>
                                        </div>
                                    </div>
                                    
                                    @if($ticket->survey->comments)
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Comentarios</p>
                                            <p class="text-sm text-gray-700">{{ $ticket->survey->comments }}</p>
                                        </div>
                                    @endif
                                    
                                    <p class="text-xs text-gray-500">Completada el {{ $ticket->survey->completed_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Pendiente de completar</p>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>

<!-- Modal para ver imágenes -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="max-w-4xl max-h-full p-4 relative">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[80vh] object-contain rounded-lg">
        <p id="modalImageName" class="text-white text-center mt-2 text-sm"></p>
    </div>
</div>

<script>
function openImageModal(imageSrc, imageName) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalImageName = document.getElementById('modalImageName');
    
    modalImage.src = imageSrc;
    modalImageName.textContent = imageName || 'Imagen';
    modal.classList.remove('hidden');
    
    // Prevenir scroll del body
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    
    // Restaurar scroll del body
    document.body.style.overflow = 'auto';
}

// Funciones para el widget de subida de imágenes de progreso
let progressSelectedFiles = []; // Array para mantener archivos acumulados

function handleProgressFileSelect(input) {
    const newFiles = Array.from(input.files);
    
    // Verificar si agregar los nuevos archivos excedería el límite
    if (progressSelectedFiles.length + newFiles.length > 3) {
        const remaining = 3 - progressSelectedFiles.length;
        if (remaining > 0) {
            alert(`Solo puedes agregar ${remaining} imagen${remaining > 1 ? 'es' : ''} más. Se agregarán las primeras ${remaining}.`);
            progressSelectedFiles = progressSelectedFiles.concat(newFiles.slice(0, remaining));
        } else {
            alert('Ya has alcanzado el límite máximo de 3 imágenes.');
            return;
        }
    } else {
        // Agregar todos los archivos nuevos
        progressSelectedFiles = progressSelectedFiles.concat(newFiles);
    }
    
    // Actualizar el input con todos los archivos
    updateProgressInputFiles();
    previewProgressImages();
}

function updateProgressInputFiles() {
    const input = document.getElementById('progress_images');
    const dt = new DataTransfer();
    
    progressSelectedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
}

function previewProgressImages() {
    const previewContainer = document.getElementById('progress-image-preview');
    
    // Limpiar vista previa anterior
    previewContainer.innerHTML = '';
    
    if (progressSelectedFiles.length > 0) {
        previewContainer.classList.remove('hidden');
        
        progressSelectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const imageContainer = document.createElement('div');
                imageContainer.className = 'relative group';
                
                imageContainer.innerHTML = `
                    <div class="aspect-w-1 aspect-h-1 w-full">
                        <img src="${e.target.result}" 
                             alt="Vista previa ${index + 1}" 
                             class="w-full h-24 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                    </div>
                    <button type="button" 
                            onclick="removeProgressImage(${index})"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        ×
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg truncate">
                        ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                    </div>
                    <div class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-bold">
                        ${index + 1}
                    </div>
                `;
                
                previewContainer.appendChild(imageContainer);
            };
            
            reader.readAsDataURL(file);
        });
        
        // Actualizar contador
        updateProgressImageCounter(progressSelectedFiles.length);
    } else {
        previewContainer.classList.add('hidden');
        updateProgressImageCounter(0);
    }
}

function removeProgressImage(index) {
    progressSelectedFiles.splice(index, 1);
    updateProgressInputFiles();
    previewProgressImages();
}

function updateProgressImageCounter(fileCount) {
    const counter = document.getElementById('progress-image-counter');
    const counterText = document.getElementById('progress-counter-text');
    const counterBar = document.getElementById('progress-counter-bar');
    const counterMessage = document.getElementById('progress-counter-message');
    const input = document.getElementById('progress_images');
    const dropZone = document.querySelector('.border-dashed');
    
    if (fileCount > 0) {
        counter.classList.remove('hidden');
        counterText.textContent = `${fileCount} / 3`;
        
        const percentage = (fileCount / 3) * 100;
        counterBar.style.width = `${percentage}%`;
        
        if (fileCount >= 3) {
            // Límite alcanzado
            counterText.classList.remove('text-gray-900');
            counterText.classList.add('text-red-600', 'font-bold');
            counterBar.classList.remove('bg-blue-600');
            counterBar.classList.add('bg-red-500');
            counterMessage.textContent = 'Límite máximo alcanzado (3 imágenes)';
            counterMessage.classList.remove('text-gray-500');
            counterMessage.classList.add('text-red-600');
            
            // Deshabilitar input y drop zone
            if (input) input.disabled = true;
            if (dropZone) {
                dropZone.classList.add('opacity-50', 'cursor-not-allowed');
                dropZone.classList.remove('cursor-pointer');
            }
        } else if (fileCount >= 2) {
            // Cerca del límite
            counterText.classList.remove('text-gray-900', 'text-red-600');
            counterText.classList.add('text-yellow-600', 'font-medium');
            counterBar.classList.remove('bg-blue-600', 'bg-red-500');
            counterBar.classList.add('bg-yellow-500');
            counterMessage.textContent = `Puedes subir ${3 - fileCount} imagen${3 - fileCount > 1 ? 'es' : ''} más`;
            counterMessage.classList.remove('text-gray-500', 'text-red-600');
            counterMessage.classList.add('text-yellow-600');
            
            // Habilitar input y drop zone
            if (input) input.disabled = false;
            if (dropZone) {
                dropZone.classList.remove('opacity-50', 'cursor-not-allowed');
                dropZone.classList.add('cursor-pointer');
            }
        } else {
            // Normal
            counterText.classList.remove('text-red-600', 'text-yellow-600', 'font-bold');
            counterText.classList.add('text-gray-900', 'font-medium');
            counterBar.classList.remove('bg-red-500', 'bg-yellow-500');
            counterBar.classList.add('bg-blue-600');
            counterMessage.textContent = `Puedes subir ${3 - fileCount} imagen${3 - fileCount > 1 ? 'es' : ''} más`;
            counterMessage.classList.remove('text-red-600', 'text-yellow-600');
            counterMessage.classList.add('text-gray-500');
            
            // Habilitar input y drop zone
            if (input) input.disabled = false;
            if (dropZone) {
                dropZone.classList.remove('opacity-50', 'cursor-not-allowed');
                dropZone.classList.add('cursor-pointer');
            }
        }
    } else {
        counter.classList.add('hidden');
        
        // Habilitar input y drop zone
        if (input) input.disabled = false;
        if (dropZone) {
            dropZone.classList.remove('opacity-50', 'cursor-not-allowed');
            dropZone.classList.add('cursor-pointer');
        }
    }
}

function previewProgressImages(input) {
    const previewContainer = document.getElementById('progress-image-preview');
    const files = input.files;
    
    // Limpiar vista previa anterior
    previewContainer.innerHTML = '';
    
    if (files.length > 0) {
        previewContainer.classList.remove('hidden');
        
        // Limitar a 3 archivos
        const limitedFiles = Array.from(files).slice(0, 3);
        
        limitedFiles.forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const imageContainer = document.createElement('div');
                imageContainer.className = 'relative group';
                
                imageContainer.innerHTML = `
                    <div class="aspect-w-1 aspect-h-1 w-full">
                        <img src="${e.target.result}" 
                             alt="Vista previa ${index + 1}" 
                             class="w-full h-24 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                    </div>
                    <button type="button" 
                            onclick="removeProgressImage(${index})"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        ×
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg">
                        ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                    </div>
                `;
                
                previewContainer.appendChild(imageContainer);
            };
            
            reader.readAsDataURL(file);
        });
        
        // Actualizar contador
        updateProgressImageCounter(limitedFiles.length);
    } else {
        previewContainer.classList.add('hidden');
        updateProgressImageCounter(0);
    }
}

function removeProgressImage(index) {
    const input = document.getElementById('progress_images');
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    // Remover el archivo del índice especificado
    files.splice(index, 1);
    
    // Recrear FileList
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;
    
    // Actualizar vista previa
    previewProgressImages(input);
}

// Cerrar modal al hacer clic fuera de la imagen
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('imageModal');
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
    }
    
    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
    
    // Drag and drop functionality para el área de subida de progreso
    const dropZone = document.querySelector('.border-dashed');
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        dropZone.addEventListener('drop', handleProgressDrop, false);
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function highlight(e) {
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    }
    
    function handleProgressDrop(e) {
        const dt = e.dataTransfer;
        const files = Array.from(dt.files);
        
        // Verificar si agregar los nuevos archivos excedería el límite
        if (progressSelectedFiles.length + files.length > 3) {
            const remaining = 3 - progressSelectedFiles.length;
            if (remaining > 0) {
                alert(`Solo puedes agregar ${remaining} imagen${remaining > 1 ? 'es' : ''} más. Se agregarán las primeras ${remaining}.`);
                progressSelectedFiles = progressSelectedFiles.concat(files.slice(0, remaining));
            } else {
                alert('Ya has alcanzado el límite máximo de 3 imágenes.');
                return;
            }
        } else {
            // Agregar todos los archivos nuevos
            progressSelectedFiles = progressSelectedFiles.concat(files);
        }
        
        // Actualizar el input con todos los archivos
        updateProgressInputFiles();
        previewProgressImages();
    }
});
</script>
@endsection