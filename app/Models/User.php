<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider_id',
        'avatar',
        'provider',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relaciones
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function ticketImages()
    {
        return $this->hasMany(TicketImage::class, 'uploaded_by');
    }

    /**
     * Verificar si el usuario es miembro del almacén
     */
    public function isAlmacen(): bool
    {
        return $this->hasRole('almacen');
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Verificar si el usuario es solicitante
     */
    public function isSolicitante(): bool
    {
        return $this->hasRole('solicitante');
    }

    /**
     * Obtener el rol principal del usuario (para compatibilidad)
     */
    public function getPrimaryRole(): string
    {
        if ($this->hasRole('admin')) {
            return 'admin';
        } elseif ($this->hasRole('almacen')) {
            return 'almacen';
        } elseif ($this->hasRole('solicitante')) {
            return 'solicitante';
        }
        
        return 'solicitante'; // rol por defecto
    }

    /**
     * Obtener todos los roles del usuario como string
     */
    public function getRolesString(): string
    {
        return $this->roles->pluck('name')->join(', ');
    }

    /**
     * Verificar si el usuario tiene al menos uno de los roles dados
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Verificar si el usuario tiene encuestas pendientes
     */
    public function hasPendingSurveys(): bool
    {
        return $this->surveys()
            ->whereNull('completed_at')
            ->exists();
    }

    /**
     * Verificar si el usuario puede crear un nuevo ticket
     */
    public function canCreateTicket(): bool
    {
        return !$this->hasPendingSurveys();
    }

    /**
     * Obtener la URL del avatar del usuario
     */
    public function getAvatarUrl(): string
    {
        return $this->avatar ?: 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Obtener las iniciales del usuario para el avatar
     */
    public function getInitials(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }
        
        return $initials ?: strtoupper(substr($this->name, 0, 2));
    }
}
