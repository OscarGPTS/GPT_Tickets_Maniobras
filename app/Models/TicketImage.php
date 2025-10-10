<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class TicketImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'uploaded_by',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'type',
        'description',
    ];

    /**
     * Tipos de imagen
     */
    const TYPE_SOLICITUD = 'solicitud';
    const TYPE_EVIDENCIA = 'evidencia';
    const TYPE_PROGRESO = 'progreso';

    /**
     * Relaciones
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scopes
     */
    public function scopeSolicitud($query)
    {
        return $query->where('type', self::TYPE_SOLICITUD);
    }

    public function scopeEvidencia($query)
    {
        return $query->where('type', self::TYPE_EVIDENCIA);
    }

    public function scopeProgreso($query)
    {
        return $query->where('type', self::TYPE_PROGRESO);
    }

    /**
     * Obtener la URL de la imagen
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Obtener el tamaño formateado
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Verificar si es imagen de solicitud
     */
    public function isSolicitud(): bool
    {
        return $this->type === self::TYPE_SOLICITUD;
    }

    /**
     * Verificar si es imagen de evidencia
     */
    public function isEvidencia(): bool
    {
        return $this->type === self::TYPE_EVIDENCIA;
    }

    /**
     * Verificar si es progreso
     */
    public function isProgreso(): bool
    {
        return $this->type === self::TYPE_PROGRESO;
    }
}
