<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCompletedNotification extends Notification implements ShouldQueue
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
            ->subject('Ticket Completado #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tu ticket ha sido completado.')
            ->line('**Ticket #' . $this->ticket->id . ':** ' . $this->ticket->title)
            ->line('**Completado por:** ' . $this->ticket->assignedTo->name)
            ->line('**Evidencia del trabajo:** ' . substr($this->ticket->work_evidence ?? 'Sin evidencia', 0, 100))
            ->action('Ver Ticket y Calificar Servicio', route('tickets.show', $this->ticket))
            ->line('Por favor, revisa el trabajo realizado y califica el servicio.')
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
            'completed_by' => $this->ticket->assignedTo->name,
            'message' => 'Tu ticket "' . $this->ticket->title . '" ha sido completado. Por favor califica el servicio.',
            'action_url' => route('tickets.show', $this->ticket),
            'type' => 'ticket_completed',
            'icon' => 'fa-check-circle',
            'color' => 'green',
        ];
    }
}
