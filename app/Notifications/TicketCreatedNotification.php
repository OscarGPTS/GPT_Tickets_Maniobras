<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo Ticket Creado #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se ha creado un nuevo ticket que requiere tu atención.')
            ->line('**Ticket #' . $this->ticket->id . ':** ' . $this->ticket->title)
            ->line('**Solicitante:** ' . $this->ticket->user->name)
            ->line('**Descripción:** ' . substr($this->ticket->description, 0, 100) . '...')
            ->action('Ver Ticket', route('almacen.tickets.show', $this->ticket))
            ->line('Por favor, revisa y asigna este ticket lo antes posible.')
            ->salutation('Saludos, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'user_name' => $this->ticket->user->name,
            'message' => 'Nuevo ticket creado: ' . $this->ticket->title,
            'action_url' => route('almacen.tickets.show', $this->ticket),
            'type' => 'ticket_created',
            'icon' => 'fa-ticket-alt',
            'color' => 'blue',
        ];
    }
}
