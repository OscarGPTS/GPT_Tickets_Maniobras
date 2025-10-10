<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCompleted extends Notification implements ShouldQueue
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
            ->subject('Tu solicitud ha sido completada')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tu solicitud de movimiento de carga ha sido completada.')
            ->line('**Título:** ' . $this->ticket->title)
            ->line('**Completada por:** ' . $this->ticket->assignedTo->name)
            ->line('**Fecha de finalización:** ' . $this->ticket->completed_at->format('d/m/Y H:i'))
            ->when($this->ticket->work_evidence, function ($mail) {
                return $mail->line('**Evidencia del trabajo:** ' . $this->ticket->work_evidence);
            })
            ->action('Ver Solicitud y Calificar', url('/tickets/' . $this->ticket->id))
            ->line('Por favor, califica el servicio completando la encuesta de satisfacción.');
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
            'completed_at' => $this->ticket->completed_at->toISOString(),
            'message' => 'Tu solicitud "' . $this->ticket->title . '" ha sido completada',
            'action_url' => url('/tickets/' . $this->ticket->id),
        ];
    }
}
