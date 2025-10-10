<?php

namespace App\Notifications;

use App\Models\Survey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SurveyPending extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Survey $survey
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
            ->subject('Encuesta de satisfacción pendiente')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tienes una encuesta de satisfacción pendiente.')
            ->line('**Solicitud:** ' . $this->survey->ticket->title)
            ->line('Para poder crear nuevas solicitudes, debes completar esta encuesta de satisfacción.')
            ->action('Completar Encuesta', url('/surveys/' . $this->survey->id))
            ->line('Tu opinión es muy importante para nosotros.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'survey_id' => $this->survey->id,
            'ticket_id' => $this->survey->ticket->id,
            'ticket_title' => $this->survey->ticket->title,
            'message' => 'Tienes una encuesta pendiente para la solicitud: ' . $this->survey->ticket->title,
            'action_url' => url('/surveys/' . $this->survey->id),
        ];
    }
}
