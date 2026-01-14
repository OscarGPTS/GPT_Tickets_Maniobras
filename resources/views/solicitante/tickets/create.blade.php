@extends('layouts.app')

@section('title', 'Crear Nueva Solicitud')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver a Mis Tickets
                </a>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Nueva Solicitud de Movimiento</h1>
            <p class="mt-1 text-sm text-gray-600">
                Describe detalladamente tu solicitud de movimiento de carga
            </p>
        </div>

        <!-- Formulario -->
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('solicitante.tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
                @csrf

                <!-- Título -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Título de la solicitud *
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               name="title" 
                               id="title" 
                               value="{{ old('title') }}"
                               required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-300 @enderror"
                               placeholder="Ej: Movimiento de pallets desde almacén A a zona de carga">
                    </div>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Descripción detallada *
                    </label>
                    <div class="mt-1">
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  required
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-300 @enderror"
                                  placeholder="Describe en detalle qué necesitas mover, desde dónde y hacia dónde...">{{ old('description') }}</textarea>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Incluye información como: tipo de mercancía, peso aproximado, origen, destino, instrucciones especiales, etc.
                    </p>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Imágenes -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700">
                        Imágenes de referencia
                    </label>
                    <div class="mt-1">
                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <div class="mx-auto h-12 w-12 text-gray-400">
                                    <i class="fas fa-cloud-upload-alt text-3xl"></i>
                                </div>
                                <div class="flex text-sm text-gray-600">
                                    <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Subir archivos</span>
                                        <input id="images" 
                                               name="images[]" 
                                               type="file" 
                                               class="sr-only" 
                                               multiple 
                                               accept="image/*"
                                               onchange="handleFileSelect(this)">
                                    </label>
                                    <p class="pl-1">o arrastra y suelta</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PNG, JPG, JPEG hasta 2MB cada una
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicador de límite de imágenes -->
                    <div id="image-counter" class="mt-2 hidden">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Imágenes seleccionadas:</span>
                            <span id="counter-text" class="font-medium text-gray-900">0 / 5</span>
                        </div>
                        <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                            <div id="counter-bar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="counter-message" class="mt-1 text-xs text-gray-500">Puedes subir hasta 5 imágenes</p>
                    </div>
                    
                    <!-- Vista previa de imágenes -->
                    <div id="image-preview" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 hidden">
                        <!-- Las imágenes se mostrarán aquí -->
                    </div>
                    
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Información adicional -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle h-5 w-5 text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Información importante
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Tu solicitud será revisada por el equipo de almacén</li>
                                    <li>Recibirás notificaciones sobre el progreso</li>
                                    <li>Una vez completada, deberás calificar el servicio</li>
                                    <li>Las imágenes ayudan a entender mejor tu solicitud</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('solicitante.tickets.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent shadow-sm text-sm font-medium rounded-md text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function handleFileSelect(input) {
    // Esta función maneja la selección de archivos a través del input
    // No necesita mantener archivos existentes porque cuando abres el selector de archivos,
    // permites al usuario seleccionar múltiples archivos de una vez
    previewImages(input);
}

function updateImageCounter(fileCount) {
    const counter = document.getElementById('image-counter');
    const counterText = document.getElementById('counter-text');
    const counterBar = document.getElementById('counter-bar');
    const counterMessage = document.getElementById('counter-message');
    const input = document.getElementById('images');
    const dropZone = document.querySelector('.border-dashed');
    
    if (fileCount > 0) {
        counter.classList.remove('hidden');
        counterText.textContent = `${fileCount} / 5`;
        
        const percentage = (fileCount / 5) * 100;
        counterBar.style.width = `${percentage}%`;
        
        if (fileCount >= 5) {
            // Límite alcanzado
            counterText.classList.remove('text-gray-900');
            counterText.classList.add('text-red-600', 'font-bold');
            counterBar.classList.remove('bg-indigo-600');
            counterBar.classList.add('bg-red-500');
            counterMessage.textContent = 'Límite máximo alcanzado (5 imágenes)';
            counterMessage.classList.remove('text-gray-500');
            counterMessage.classList.add('text-red-600');
            
            // Deshabilitar input y drop zone
            input.disabled = true;
            dropZone.classList.add('opacity-50', 'cursor-not-allowed');
            dropZone.classList.remove('cursor-pointer');
        } else if (fileCount >= 4) {
            // Cerca del límite
            counterText.classList.remove('text-gray-900', 'text-red-600');
            counterText.classList.add('text-yellow-600', 'font-medium');
            counterBar.classList.remove('bg-indigo-600', 'bg-red-500');
            counterBar.classList.add('bg-yellow-500');
            counterMessage.textContent = `Puedes subir ${5 - fileCount} imagen${5 - fileCount > 1 ? 'es' : ''} más`;
            counterMessage.classList.remove('text-gray-500', 'text-red-600');
            counterMessage.classList.add('text-yellow-600');
            
            // Habilitar input y drop zone
            input.disabled = false;
            dropZone.classList.remove('opacity-50', 'cursor-not-allowed');
            dropZone.classList.add('cursor-pointer');
        } else {
            // Normal
            counterText.classList.remove('text-red-600', 'text-yellow-600', 'font-bold');
            counterText.classList.add('text-gray-900', 'font-medium');
            counterBar.classList.remove('bg-red-500', 'bg-yellow-500');
            counterBar.classList.add('bg-indigo-600');
            counterMessage.textContent = `Puedes subir ${5 - fileCount} imagen${5 - fileCount > 1 ? 'es' : ''} más`;
            counterMessage.classList.remove('text-red-600', 'text-yellow-600');
            counterMessage.classList.add('text-gray-500');
            
            // Habilitar input y drop zone
            input.disabled = false;
            dropZone.classList.remove('opacity-50', 'cursor-not-allowed');
            dropZone.classList.add('cursor-pointer');
        }
    } else {
        counter.classList.add('hidden');
        
        // Habilitar input y drop zone
        input.disabled = false;
        dropZone.classList.remove('opacity-50', 'cursor-not-allowed');
        dropZone.classList.add('cursor-pointer');
    }
}

function previewImages(input) {
    const previewContainer = document.getElementById('image-preview');
    const files = input.files;
    
    // Limpiar vista previa anterior
    previewContainer.innerHTML = '';
    
    if (files.length > 0) {
        previewContainer.classList.remove('hidden');
        
        // Limitar a 5 archivos
        const maxFiles = Math.min(files.length, 5);
        
        for (let i = 0; i < maxFiles; i++) {
            const file = files[i];
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const imageDiv = document.createElement('div');
                    imageDiv.className = 'relative group';
                    imageDiv.innerHTML = `
                        <img src="${e.target.result}" 
                             alt="Vista previa" 
                             class="h-24 w-full object-cover rounded-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                            <button type="button" 
                                    onclick="removeImage(this, ${i})"
                                    class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:scale-110 transform transition-transform">
                                <i class="fas fa-times-circle text-xl"></i>
                            </button>
                        </div>
                        <div class="absolute bottom-1 left-1 bg-black bg-opacity-75 text-white text-xs px-1 rounded">
                            ${file.name.length > 15 ? file.name.substring(0, 12) + '...' : file.name}
                        </div>
                    `;
                    previewContainer.appendChild(imageDiv);
                };
                
                reader.readAsDataURL(file);
            }
        }
    } else {
        previewContainer.classList.add('hidden');
    }
    
    // Actualizar contador
    updateImageCounter(files.length);
}

function removeImage(button, index) {
    const input = document.getElementById('images');
    const dt = new DataTransfer();
    
    // Recrear FileList sin el archivo removido
    for (let i = 0; i < input.files.length; i++) {
        if (i !== index) {
            dt.items.add(input.files[i]);
        }
    }
    
    input.files = dt.files;
    previewImages(input);
}

// Drag and drop functionality
const dropZone = document.querySelector('.border-dashed');

dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    if (!document.getElementById('images').disabled) {
        this.classList.add('border-indigo-400', 'bg-indigo-50');
    }
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('border-indigo-400', 'bg-indigo-50');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-indigo-400', 'bg-indigo-50');
    
    const input = document.getElementById('images');
    
    // Si el input está deshabilitado (límite alcanzado), no hacer nada
    if (input.disabled) {
        return;
    }
    
    const newFiles = e.dataTransfer.files;
    const dt = new DataTransfer();
    
    // Agregar archivos existentes
    for (let i = 0; i < input.files.length; i++) {
        dt.items.add(input.files[i]);
    }
    
    // Agregar nuevos archivos solo si no excede el límite
    for (let i = 0; i < newFiles.length; i++) {
        if (dt.files.length < 5) {
            dt.items.add(newFiles[i]);
        }
    }
    
    input.files = dt.files;
    previewImages(input);
});
</script>
@endpush
@endsection
