<?php

namespace App\Notifications;

use App\Models\Survey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SurveyCompletedNotification extends Notification
{
    use Queueable;

    protected $survey;

    /**
     * Create a new notification instance.
     */
    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
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
            ->subject('[Movimiento de Carga] Ticket calificado #' . $this->survey->ticket->id)
            ->view('emails.tickets.survey-completed', [
                'ticket' => $this->survey->ticket,
                'survey' => $this->survey,
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
        $ticket = $this->survey->ticket;
        
        return [
            'survey_id' => $this->survey->id,
            'ticket_id' => $ticket->id,
            'ticket_title' => $ticket->title,
            'rating' => $this->survey->rating,
            'user_name' => $ticket->user->name,
            'message' => $ticket->user->name . ' calificó el ticket "' . $ticket->title . '" con ' . $this->survey->rating . ' estrellas',
            'action_url' => route('almacen.tickets.show', $ticket),
            'type' => 'survey_completed',
            'icon' => 'fa-star',
            'color' => 'yellow',
        ];
    }
}
