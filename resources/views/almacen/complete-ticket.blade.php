@extends('layouts.app')

@section('title', 'Completar Ticket #' . $ticket->id)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Completar Ticket #{{ $ticket->id }}</h1>
                        <p class="text-gray-600 mt-2">{{ $ticket->title }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('almacen.tickets.show', $ticket) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Ticket -->
        <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Ticket</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Solicitante</p>
                    <p class="text-gray-900">{{ $ticket->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Fecha de Creación</p>
                    <p class="text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500">Descripción</p>
                    <p class="text-gray-900">{{ $ticket->description }}</p>
                </div>
            </div>
        </div>

        <!-- Mensajes de Error -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Se encontraron los siguientes errores:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulario para Completar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Evidencia del Trabajo Realizado</h3>
                <p class="text-sm text-gray-600 mt-1">Describe el trabajo realizado y adjunta evidencia fotográfica</p>
            </div>

            <form method="POST" action="{{ route('almacen.tickets.complete', $ticket) }}" enctype="multipart/form-data" class="p-6">
                @csrf

                <!-- Descripción del Trabajo -->
                <div class="mb-6">
                    <label for="work_evidence" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción del Trabajo Realizado <span class="text-red-500">*</span>
                    </label>
                    <textarea name="work_evidence" 
                              id="work_evidence" 
                              rows="6"
                              required
                              placeholder="Describe detalladamente el trabajo que realizaste para resolver este ticket..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('work_evidence') border-red-300 @enderror">{{ old('work_evidence') }}</textarea>
                    @error('work_evidence')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Incluye detalles específicos sobre las acciones tomadas, herramientas utilizadas, y el resultado final.
                    </p>
                </div>

                <!-- Imágenes de Evidencia -->
                <div class="mb-6">
                    <label for="evidence_images" class="block text-sm font-medium text-gray-700 mb-2">
                        Imágenes de Evidencia (Opcional)
                    </label>
                    <div class="mt-1">
                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-green-400 transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="evidence_images" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                        <span>Agregar imágenes</span>
                                        <input id="evidence_images" 
                                               name="evidence_images[]" 
                                               type="file" 
                                               class="sr-only" 
                                               multiple 
                                               accept="image/*"
                                               onchange="handleEvidenceFileSelect(this)">
                                    </label>
                                    <p class="pl-1">o arrastra y suelta</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 2MB cada una (máximo 5 imágenes)</p>
                                <p class="text-xs text-green-600 font-medium mt-1">
                                    💡 Las imágenes se acumulan - puedes agregar más sin perder las anteriores
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicador de límite de imágenes -->
                    <div id="evidence-image-counter" class="mt-2 hidden">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Imágenes seleccionadas:</span>
                            <span id="evidence-counter-text" class="font-medium text-gray-900">0 / 5</span>
                        </div>
                        <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                            <div id="evidence-counter-bar" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="evidence-counter-message" class="mt-1 text-xs text-gray-500">Puedes subir hasta 5 imágenes</p>
                    </div>
                    
                    <!-- Vista previa de imágenes -->
                    <div id="evidence-image-preview" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 hidden">
                        <!-- Las imágenes se mostrarán aquí -->
                    </div>
                    
                    @error('evidence_images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('evidence_images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Información Importante -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">¿Qué sucede al completar?</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>El ticket se marcará como <strong>completado</strong></li>
                                    <li>Se notificará al usuario solicitante</li>
                                    <li>Se creará automáticamente una encuesta de satisfacción</li>
                                    <li>Las imágenes y descripción quedarán como evidencia del trabajo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('almacen.tickets.show', $ticket) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Completar Ticket
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
// Funciones para el widget de evidencia
let evidenceSelectedFiles = []; // Array para mantener archivos acumulados

function handleEvidenceFileSelect(input) {
    const newFiles = Array.from(input.files);
    
    // Verificar si agregar los nuevos archivos excedería el límite
    if (evidenceSelectedFiles.length + newFiles.length > 5) {
        const remaining = 5 - evidenceSelectedFiles.length;
        if (remaining > 0) {
            alert(`Solo puedes agregar ${remaining} imagen${remaining > 1 ? 'es' : ''} más. Se agregarán las primeras ${remaining}.`);
            evidenceSelectedFiles = evidenceSelectedFiles.concat(newFiles.slice(0, remaining));
        } else {
            alert('Ya has alcanzado el límite máximo de 5 imágenes.');
            return;
        }
    } else {
        // Agregar todos los archivos nuevos
        evidenceSelectedFiles = evidenceSelectedFiles.concat(newFiles);
    }
    
    // Actualizar el input con todos los archivos
    updateEvidenceInputFiles();
    previewEvidenceImages();
}

function updateEvidenceInputFiles() {
    const input = document.getElementById('evidence_images');
    const dt = new DataTransfer();
    
    evidenceSelectedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
}

function previewEvidenceImages() {
    const previewContainer = document.getElementById('evidence-image-preview');
    
    // Limpiar vista previa anterior
    previewContainer.innerHTML = '';
    
    if (evidenceSelectedFiles.length > 0) {
        previewContainer.classList.remove('hidden');
        
        evidenceSelectedFiles.forEach((file, index) => {
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
                            onclick="removeEvidenceImage(${index})"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        ×
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg truncate">
                        ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                    </div>
                    <div class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-bold">
                        ${index + 1}
                    </div>
                `;
                
                previewContainer.appendChild(imageContainer);
            };
            
            reader.readAsDataURL(file);
        });
        
        // Actualizar contador
        updateEvidenceImageCounter(evidenceSelectedFiles.length);
    } else {
        previewContainer.classList.add('hidden');
        updateEvidenceImageCounter(0);
    }
}

function removeEvidenceImage(index) {
    evidenceSelectedFiles.splice(index, 1);
    updateEvidenceInputFiles();
    previewEvidenceImages();
}

function updateEvidenceImageCounter(fileCount) {
    const counter = document.getElementById('evidence-image-counter');
    const counterText = document.getElementById('evidence-counter-text');
    const counterBar = document.getElementById('evidence-counter-bar');
    const counterMessage = document.getElementById('evidence-counter-message');
    const input = document.getElementById('evidence_images');
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
            counterBar.classList.remove('bg-green-600');
            counterBar.classList.add('bg-red-500');
            counterMessage.textContent = 'Límite máximo alcanzado (5 imágenes)';
            counterMessage.classList.remove('text-gray-500');
            counterMessage.classList.add('text-red-600');
            
            // Deshabilitar input y drop zone
            if (input) input.disabled = true;
            if (dropZone) {
                dropZone.classList.add('opacity-50', 'cursor-not-allowed');
                dropZone.classList.remove('cursor-pointer');
            }
        } else if (fileCount >= 4) {
            // Cerca del límite
            counterText.classList.remove('text-gray-900', 'text-red-600');
            counterText.classList.add('text-yellow-600', 'font-medium');
            counterBar.classList.remove('bg-green-600', 'bg-red-500');
            counterBar.classList.add('bg-yellow-500');
            counterMessage.textContent = `Puedes subir ${5 - fileCount} imagen${5 - fileCount > 1 ? 'es' : ''} más`;
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
            counterBar.classList.add('bg-green-600');
            counterMessage.textContent = `Puedes subir ${5 - fileCount} imagen${5 - fileCount > 1 ? 'es' : ''} más`;
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

// Validación antes de enviar
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const workEvidence = document.getElementById('work_evidence').value.trim();
            
            if (workEvidence.length < 10) {
                e.preventDefault();
                alert('Por favor, proporciona una descripción más detallada del trabajo realizado (mínimo 10 caracteres).');
                return false;
            }
        });
    }
    
    // Drag and drop functionality
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
        
        dropZone.addEventListener('drop', handleEvidenceDrop, false);
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function highlight(e) {
        dropZone.classList.add('border-green-500', 'bg-green-50');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('border-green-500', 'bg-green-50');
    }
    
    function handleEvidenceDrop(e) {
        const dt = e.dataTransfer;
        const files = Array.from(dt.files);
        
        // Verificar si agregar los nuevos archivos excedería el límite
        if (evidenceSelectedFiles.length + files.length > 5) {
            const remaining = 5 - evidenceSelectedFiles.length;
            if (remaining > 0) {
                alert(`Solo puedes agregar ${remaining} imagen${remaining > 1 ? 'es' : ''} más. Se agregarán las primeras ${remaining}.`);
                evidenceSelectedFiles = evidenceSelectedFiles.concat(files.slice(0, remaining));
            } else {
                alert('Ya has alcanzado el límite máximo de 5 imágenes.');
                return;
            }
        } else {
            // Agregar todos los archivos nuevos
            evidenceSelectedFiles = evidenceSelectedFiles.concat(files);
        }
        
        // Actualizar el input con todos los archivos
        updateEvidenceInputFiles();
        previewEvidenceImages();
    }
});
</script>
@endsection