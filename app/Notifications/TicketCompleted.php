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
        // return ['mail', 'database']; // Descomentar para activar emails
        return ['database']; // Solo base de datos por ahora
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ticket completado #' . $this->ticket->id)
            ->view('emails.tickets.completed', [
                'ticket' => $this->ticket,
                'recipient' => $notifiable,
            ]);
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
