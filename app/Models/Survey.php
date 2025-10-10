<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'rating',
        'feedback',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed_at' => 'datetime',
        'rating' => 'integer',
    ];

    /**
     * Relaciones
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopePendientes($query)
    {
        return $query->whereNull('completed_at');
    }

    public function scopeCompletadas($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Verificar si la encuesta está completada
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Verificar si la encuesta está pendiente
     */
    public function isPending(): bool
    {
        return is_null($this->completed_at);
    }

    /**
     * Completar la encuesta
     */
    public function complete(): void
    {
        $this->update([
            'completed_at' => now(),
        ]);
    }

    /**
     * Obtener las estrellas de calificación
     */
    public function getStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="fas fa-star text-yellow-400"></i>';
            } else {
                $stars .= '<i class="far fa-star text-gray-300"></i>';
            }
        }
        return $stars;
    }

    /**
     * Obtener el texto de la calificación
     */
    public function getRatingTextAttribute(): string
    {
        return match($this->rating) {
            1 => 'Muy malo',
            2 => 'Malo',
            3 => 'Regular',
            4 => 'Bueno',
            5 => 'Excelente',
            default => 'Sin calificar',
        };
    }
}
