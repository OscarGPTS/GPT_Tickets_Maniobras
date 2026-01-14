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
        // return ['mail', 'database']; // Descomentar para activar emails
        return ['database']; // Solo base de datos por ahora
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ticket en progreso #' . $this->ticket->id)
            ->view('emails.tickets.in-progress', [
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
            'status' => $this->ticket->status,
            'progress_comment' => $this->progressComment,
            'assigned_to' => $this->ticket->assignedTo->name ?? 'Sin asignar',
        ];
    }
}
