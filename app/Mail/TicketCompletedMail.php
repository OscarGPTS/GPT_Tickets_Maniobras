<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, $recipient)
    {
        $this->ticket = $ticket;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Obtener todos los admins para CC
        $admins = \App\Models\User::role('admin')->get();
        $ccRecipients = $admins->pluck('email')->toArray();
        
        // Agregar al asignado en CC si existe y no es el destinatario principal
        if ($this->ticket->assignedTo && $this->ticket->assignedTo->email !== $this->recipient->email) {
            $ccRecipients[] = $this->ticket->assignedTo->email;
        }
        
        // Remover duplicados
        $ccRecipients = array_unique($ccRecipients);

        return new Envelope(
            subject: '[Movimiento de Carga] Ticket Completado #' . $this->ticket->id,
            cc: $ccRecipients
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.completed',
            with: [
                'ticket' => $this->ticket,
                'recipient' => $this->recipient,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
