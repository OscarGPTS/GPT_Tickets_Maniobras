<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Ticket $ticket
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu solicitud ha sido asignada')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tu solicitud de movimiento de carga ha sido asignada a un miembro del equipo de almacén.')
            ->line('**Título:** ' . $this->ticket->title)
            ->line('**Asignada a:** ' . $this->ticket->assignedTo->name)
            ->line('**Estado:** En proceso')
            ->action('Ver Solicitud', url('/tickets/' . $this->ticket->id))
            ->line('Te notificaremos cuando el trabajo sea completado.');
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
            'assigned_to' => $this->ticket->assignedTo->name,
            'message' => 'Tu solicitud "' . $this->ticket->title . '" ha sido asignada a ' . $this->ticket->assignedTo->name,
            'action_url' => url('/tickets/' . $this->ticket->id),
        ];
    }
}
