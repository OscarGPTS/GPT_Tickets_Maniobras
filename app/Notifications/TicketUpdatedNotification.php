<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUpdatedNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $updatedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, $updatedBy)
    {
        $this->ticket = $ticket;
        $this->updatedBy = $updatedBy;
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
            ->subject('[Movimiento de Carga] Ticket Editado #' . $this->ticket->id)
            ->line('El ticket #' . $this->ticket->id . ' ha sido editado.')
            ->line('**Título:** ' . $this->ticket->title)
            ->line('**Editado por:** ' . $this->updatedBy->name)
            ->action('Ver Ticket', route('admin.tickets.show', $this->ticket))
            ->line('Por favor revisa los cambios realizados.');
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
            'updated_by_name' => $this->updatedBy->name,
            'message' => $this->updatedBy->name . ' editó el ticket: ' . $this->ticket->title,
            'action_url' => route('admin.tickets.show', $this->ticket),
            'type' => 'ticket_updated',
            'icon' => 'fa-edit',
            'color' => 'yellow',
        ];
    }
}
