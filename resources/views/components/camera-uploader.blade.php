<div x-data="cameraUploader()" class="space-y-4">
    <!-- Opciones de carga -->
    <div class="flex items-center space-x-4">
        <button type="button" @click="startCamera()" x-show="!cameraActive"
                class="inline-flex items-center px-4 py-2 border border-indigo-600 shadow-sm text-sm font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-camera mr-2"></i>
            Abrir Cámara
        </button>
        
        <button type="button" @click="stopCamera()" x-show="cameraActive"
                class="inline-flex items-center px-4 py-2 border border-red-600 shadow-sm text-sm font-medium rounded-md text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <i class="fas fa-times mr-2"></i>
            Cerrar Cámara
        </button>
        
        <label class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer">
            <i class="fas fa-upload mr-2"></i>
            Subir Archivos
            <input type="file" accept="image/*" multiple 
                   @change="handleFileSelect($event)"
                   class="sr-only">
        </label>
    </div>

    <!-- Vista de cámara -->
    <div x-show="cameraActive" class="relative bg-black rounded-lg overflow-hidden">
        <video x-ref="video" autoplay playsinline class="w-full max-h-96 object-cover"></video>
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex items-center space-x-4">
            <button type="button" @click="capturePhoto()"
                    class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-white hover:bg-gray-100 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <div class="h-12 w-12 rounded-full border-4 border-gray-800"></div>
            </button>
        </div>
    </div>

    <!-- Canvas oculto para captura -->
    <canvas x-ref="canvas" class="hidden"></canvas>

    <!-- Previsualización de imágenes -->
    <div x-show="images.length > 0" class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Imágenes seleccionadas (<span x-text="images.length"></span>/<span x-text="maxImages"></span>)
        </label>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <template x-for="(image, index) in images" :key="index">
                <div class="relative group">
                    <img :src="image.preview" alt="Preview" 
                         class="h-32 w-full object-cover rounded-lg border-2 border-gray-200">
                    <button type="button" @click="removeImage(index)"
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                        <span x-text="(image.size / 1024).toFixed(0)"></span> KB
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Inputs hidden para envío -->
    <template x-for="(image, index) in images" :key="index">
        <input type="hidden" 
               :name="inputName + '[' + index + ']'" 
               :value="image.data">
    </template>
</div>

@push('scripts')
<script>
function cameraUploader() {
    return {
        cameraActive: false,
        stream: null,
        images: [],
        maxImages: {{ $maxImages ?? 5 }},
        inputName: '{{ $inputName ?? "images" }}',
        
        startCamera() {
            if (this.images.length >= this.maxImages) {
                alert('Has alcanzado el límite de ' + this.maxImages + ' imágenes');
                return;
            }
            
            navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                } 
            })
            .then(stream => {
                this.stream = stream;
                this.cameraActive = true;
                this.$nextTick(() => {
                    this.$refs.video.srcObject = stream;
                });
            })
            .catch(err => {
                console.error('Error al acceder a la cámara:', err);
                alert('No se pudo acceder a la cámara. Por favor verifica los permisos.');
            });
        },
        
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.cameraActive = false;
        },
        
        capturePhoto() {
            if (this.images.length >= this.maxImages) {
                alert('Has alcanzado el límite de ' + this.maxImages + ' imágenes');
                return;
            }
            
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            const context = canvas.getContext('2d');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            canvas.toBlob(blob => {
                const reader = new FileReader();
                reader.onloadend = () => {
                    this.images.push({
                        preview: reader.result,
                        data: reader.result,
                        size: blob.size,
                        name: 'camera-' + Date.now() + '.jpg'
                    });
                    
                    if (this.images.length >= this.maxImages) {
                        this.stopCamera();
                    }
                };
                reader.readAsDataURL(blob);
            }, 'image/jpeg', 0.8);
        },
        
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            const availableSlots = this.maxImages - this.images.length;
            
            if (files.length > availableSlots) {
                alert('Solo puedes agregar ' + availableSlots + ' imagen(es) más');
                return;
            }
            
            files.forEach(file => {
                if (file.size > 2048 * 1024) {
                    alert('El archivo ' + file.name + ' es demasiado grande (máx 2MB)');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.images.push({
                        preview: e.target.result,
                        data: e.target.result,
                        size: file.size,
                        name: file.name
                    });
                };
                reader.readAsDataURL(file);
            });
            
            event.target.value = '';
        },
        
        removeImage(index) {
            this.images.splice(index, 1);
        },
        
        init() {
            // Limpiar cámara al destruir el componente
            this.$watch('cameraActive', value => {
                if (!value && this.stream) {
                    this.stopCamera();
                }
            });
        }
    };
}
</script>
@endpush
