<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'request_type',
        'description',
        'status',
        'admin_notes',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Obtener el texto descriptivo del tipo de solicitud
     */
    public function getRequestTypeText(): string
    {
        return match($this->request_type) {
            'eliminar_datos' => 'Eliminación de Datos Personales',
            'recuperar_contrasena' => 'Recuperar Contraseña',
            'cambiar_email' => 'Cambiar Correo Electrónico',
            'consulta_general' => 'Consulta General',
            'otro' => 'Otro',
            default => $this->request_type,
        };
    }

    /**
     * Obtener el texto descriptivo del estado
     */
    public function getStatusText(): string
    {
        return match($this->status) {
            'pendiente' => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'completado' => 'Completado',
            'cerrado' => 'Cerrado',
            default => $this->status,
        };
    }

    /**
     * Obtener badge class según el estado
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pendiente' => 'bg-yellow-100 text-yellow-800',
            'en_proceso' => 'bg-blue-100 text-blue-800',
            'completado' => 'bg-green-100 text-green-800',
            'cerrado' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

