<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification implements ShouldQueue
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
            ->subject('Ticket Asignado #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tu ticket ha sido asignado a un miembro del equipo de almacén.')
            ->line('**Ticket #' . $this->ticket->id . ':** ' . $this->ticket->title)
            ->line('**Asignado a:** ' . $this->ticket->assignedTo->name)
            ->line('**Estado:** ' . $this->ticket->getStatusText())
            ->action('Ver Ticket', route('tickets.show', $this->ticket))
            ->line('Recibirás una notificación cuando el ticket sea completado.')
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
            'assigned_to' => $this->ticket->assignedTo->name,
            'message' => 'Tu ticket "' . $this->ticket->title . '" ha sido asignado a ' . $this->ticket->assignedTo->name,
            'action_url' => route('tickets.show', $this->ticket),
            'type' => 'ticket_assigned',
            'icon' => 'fa-user-check',
            'color' => 'indigo',
        ];
    }
}
