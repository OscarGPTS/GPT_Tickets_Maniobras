<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Tipos de archivo permitidos para imágenes
     */
    const ALLOWED_IMAGE_TYPES = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
    
    /**
     * Tamaño máximo de archivo en bytes (2MB)
     */
    const MAX_FILE_SIZE = 2048 * 1024;

    /**
     * Subir una imagen del ticket
     */
    public function uploadTicketImage(UploadedFile $file, int $ticketId, string $type = 'solicitud'): array
    {
        $this->validateImage($file);
        
        $filename = $this->generateUniqueFilename($file);
        $directory = $type === 'evidencia' ? "tickets/{$ticketId}/evidence" : "tickets/{$ticketId}";
        $path = $file->storeAs($directory, $filename, 'public');
        
        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'filename' => $filename,
        ];
    }

    /**
     * Eliminar archivo del storage
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }

    /**
     * Validar que el archivo sea una imagen válida
     */
    private function validateImage(UploadedFile $file): void
    {
        // Verificar que es una imagen
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('El archivo no es válido.');
        }

        // Verificar tipo MIME
        $mimeType = $file->getMimeType();
        if (!str_starts_with($mimeType, 'image/')) {
            throw new \InvalidArgumentException('El archivo debe ser una imagen.');
        }

        // Verificar extensión
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_IMAGE_TYPES)) {
            throw new \InvalidArgumentException('Tipo de imagen no permitido. Tipos permitidos: ' . implode(', ', self::ALLOWED_IMAGE_TYPES));
        }

        // Verificar tamaño
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('El archivo es demasiado grande. Tamaño máximo: 2MB.');
        }

        // Verificar que realmente es una imagen (usando getimagesize)
        $imageInfo = @getimagesize($file->getPathname());
        if ($imageInfo === false) {
            throw new \InvalidArgumentException('El archivo no es una imagen válida.');
        }
    }

    /**
     * Generar nombre único para el archivo
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $hash = hash('sha256', $file->getClientOriginalName() . time() . random_bytes(16));
        
        return substr($hash, 0, 32) . '.' . $extension;
    }

    /**
     * Obtener URL pública del archivo
     */
    public function getPublicUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    /**
     * Verificar si un archivo existe
     */
    public function fileExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    /**
     * Obtener información del archivo
     */
    public function getFileInfo(string $path): ?array
    {
        if (!$this->fileExists($path)) {
            return null;
        }

        $fullPath = Storage::disk('public')->path($path);
        
        return [
            'path' => $path,
            'size' => filesize($fullPath),
            'mime_type' => mime_content_type($fullPath),
            'url' => $this->getPublicUrl($path),
            'last_modified' => filemtime($fullPath),
        ];
    }

    /**
     * Limpiar archivos huérfanos (archivos sin referencia en BD)
     */
    public function cleanOrphanFiles(): int
    {
        $deletedCount = 0;
        $ticketDirectories = Storage::disk('public')->directories('tickets');
        
        foreach ($ticketDirectories as $directory) {
            $ticketId = basename($directory);
            
            // Verificar si el ticket existe
            $ticketExists = \App\Models\Ticket::where('id', $ticketId)->exists();
            
            if (!$ticketExists) {
                Storage::disk('public')->deleteDirectory($directory);
                $deletedCount++;
            }
        }
        
        return $deletedCount;
    }

    /**
     * Obtener tamaño formateado de un archivo
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Redimensionar imagen si es necesario (requires intervention/image package)
     */
    public function resizeImageIfNeeded(UploadedFile $file, int $maxWidth = 1920, int $maxHeight = 1080): UploadedFile
    {
        // Esta funcionalidad requeriría el paquete intervention/image
        // Por ahora solo retornamos el archivo original
        return $file;
    }
}