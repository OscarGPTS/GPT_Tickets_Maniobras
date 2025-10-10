<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'work_evidence',
        'assigned_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Estados posibles del ticket
     */
    const STATUS_PENDIENTE = 'pendiente';
    const STATUS_EN_PROCESO = 'en_proceso';
    const STATUS_FINALIZADO = 'finalizado';

    /**
     * Relaciones
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function images()
    {
        return $this->hasMany(TicketImage::class);
    }

    public function solicitudImages()
    {
        return $this->hasMany(TicketImage::class)->where('type', 'solicitud');
    }

    public function evidenciaImages()
    {
        return $this->hasMany(TicketImage::class)->where('type', 'evidencia');
    }

    public function survey()
    {
        return $this->hasOne(Survey::class);
    }

    /**
     * Scopes
     */
    public function scopePendientes($query)
    {
        return $query->where('status', self::STATUS_PENDIENTE);
    }

    public function scopeEnProceso($query)
    {
        return $query->where('status', self::STATUS_EN_PROCESO);
    }

    public function scopeFinalizados($query)
    {
        return $query->where('status', self::STATUS_FINALIZADO);
    }

    /**
     * Métodos de estado
     */
    public function isPendiente(): bool
    {
        return $this->status === self::STATUS_PENDIENTE;
    }

    public function isEnProceso(): bool
    {
        return $this->status === self::STATUS_EN_PROCESO;
    }

    public function isFinalizado(): bool
    {
        return $this->status === self::STATUS_FINALIZADO;
    }

    /**
     * Asignar ticket a un usuario
     */
    public function assignTo(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
            'status' => self::STATUS_EN_PROCESO,
            'assigned_at' => now(),
        ]);
    }

    /**
     * Finalizar ticket
     */
    public function complete(): void
    {
        $this->update([
            'status' => self::STATUS_FINALIZADO,
            'completed_at' => now(),
        ]);
    }

    /**
     * Obtener el badge de estado
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDIENTE => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pendiente</span>',
            self::STATUS_EN_PROCESO => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">En Proceso</span>',
            self::STATUS_FINALIZADO => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Finalizado</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Desconocido</span>',
        };
    }

    /**
     * Obtener las clases CSS para el badge de estado
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_PENDIENTE => 'bg-yellow-100 text-yellow-800',
            self::STATUS_EN_PROCESO => 'bg-blue-100 text-blue-800',
            self::STATUS_FINALIZADO => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Obtener el texto del estado
     */
    public function getStatusText(): string
    {
        return match($this->status) {
            self::STATUS_PENDIENTE => 'Pendiente',
            self::STATUS_EN_PROCESO => 'En Proceso',
            self::STATUS_FINALIZADO => 'Finalizado',
            default => 'Desconocido',
        };
    }
}
