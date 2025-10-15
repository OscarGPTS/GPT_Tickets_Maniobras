<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCancelledNotification extends Notification implements ShouldQueue
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
            ->subject('Ticket Cancelado #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('El ticket que estabas atendiendo ha sido cancelado por el solicitante.')
            ->line('**Ticket #' . $this->ticket->id . ':** ' . $this->ticket->title)
            ->line('**Solicitante:** ' . $this->ticket->user->name)
            ->line('**Razón de cancelación:** ' . ($this->ticket->cancellation_reason ?? 'No especificada'))
            ->action('Ver Ticket', route('almacen.tickets.show', $this->ticket))
            ->line('Este ticket ya no requiere más acciones de tu parte.')
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
            'cancellation_reason' => $this->ticket->cancellation_reason,
            'message' => 'El ticket "' . $this->ticket->title . '" ha sido cancelado por ' . $this->ticket->user->name,
            'action_url' => route('almacen.tickets.show', $this->ticket),
            'type' => 'ticket_cancelled',
            'icon' => 'fa-times-circle',
            'color' => 'red',
        ];
    }
}
