<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketProgress extends Notification
{
    use Queueable;

    public $ticket;
    public $progressComment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, $progressComment = null)
    {
        $this->ticket = $ticket;
        $this->progressComment = $progressComment;
    }

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
            ->subject('Progreso en tu Ticket #' . $this->ticket->id)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Hay una actualización en tu ticket #' . $this->ticket->id . ': ' . $this->ticket->title)
            ->when($this->progressComment, function ($mail) {
                return $mail->line('Comentario del técnico: ' . $this->progressComment);
            })
            ->line('Estado actual: ' . ucfirst(str_replace('_', ' ', $this->ticket->status)))
            ->action('Ver Ticket', url('/tickets/' . $this->ticket->id))
            ->line('Gracias por usar nuestro sistema de tickets.');
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
            'status' => $this->ticket->status,
            'progress_comment' => $this->progressComment,
            'assigned_to' => $this->ticket->assignedTo->name ?? 'Sin asignar',
        ];
    }
}
