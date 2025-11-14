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
        // return ['mail', 'database']; // Descomentar para activar emails
        return ['database']; // Solo base de datos por ahora
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ticket cancelado #' . $this->ticket->id)
            ->view('emails.tickets.cancelled', [
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
