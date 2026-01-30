<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para enviar notificaciones FCM a usuarios
 */
class FCMService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Enviar notificación a un usuario específico
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = [], ?string $image = null): array
    {
        try {
            $topic = "user_{$userId}";
            
            $notification = Notification::create($title, $body);
            
            if ($image) {
                $notification = $notification->withImageUrl($image);
            }

            $messageData = [
                'topic' => $topic,
                'notification' => $notification,
            ];

            if (!empty($data)) {
                $messageData['data'] = $data;
            }

            $message = CloudMessage::fromArray($messageData)
                ->withAndroidConfig($this->getAndroidConfig())
                ->withApnsConfig($this->getApnsConfig());

            $result = $this->messaging->send($message);

            Log::info("FCM: Notificación enviada al usuario {$userId}");

            return [
                'success' => true,
                'message_id' => $result,
                'topic' => $topic
            ];

        } catch (\Exception $e) {
            Log::error("FCM: Error - " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Enviar a múltiples usuarios
     */
    public function sendToMultipleUsers(array $userIds, string $title, string $body, array $data = [], ?string $image = null): array
    {
        $results = [];
        $successCount = 0;

        foreach ($userIds as $userId) {
            $result = $this->sendToUser($userId, $title, $body, $data, $image);
            
            if ($result['success']) $successCount++;
            
            $results[] = array_merge($result, ['user_id' => $userId]);
        }

        return [
            'total' => count($userIds),
            'success_count' => $successCount,
            'results' => $results
        ];
    }

    /**
     * Notificación de ticket creado
     */
    public function notifyTicketCreated(int $userId, int $ticketId, string $ticketTitle): array
    {
        return $this->sendToUser(
            $userId,
            'Ticket Creado',
            "Tu ticket #{$ticketId} ha sido creado: {$ticketTitle}",
            ['type' => 'ticket_created', 'ticket_id' => (string) $ticketId]
        );
    }

    /**
     * Notificación de nueva solicitud a admins
     */
    public function notifyNewTicketToAdmins(int $ticketId, string $ticketTitle, string $solicitante): array
    {
        // Obtener todos los usuarios admin
        $admins = \App\Models\User::role('admin')->pluck('id')->toArray();
        
        if (empty($admins)) {
            return ['success' => false, 'message' => 'No hay administradores para notificar'];
        }

        return $this->sendToMultipleUsers(
            $admins,
            '📦 Nueva Solicitud',
            "Tienes una nueva solicitud de movimiento de carga de {$solicitante}",
            [
                'type' => 'new_ticket_request',
                'ticket_id' => (string) $ticketId,
                'action' => 'review_ticket'
            ]
        );
    }

    /**
     * Notificación de ticket asignado
     */
    public function notifyTicketAssigned(int $userId, int $ticketId, string $ticketTitle): array
    {
        return $this->sendToUser(
            $userId,
            'Ticket Asignado',
            "Se te ha asignado el ticket #{$ticketId}: {$ticketTitle}",
            ['type' => 'ticket_assigned', 'ticket_id' => (string) $ticketId]
        );
    }

    /**
     * Notificación de cambio de estado
     */
    public function notifyTicketStatusChanged(int $userId, int $ticketId, string $newStatus): array
    {
        return $this->sendToUser(
            $userId,
            'Actualización de Ticket',
            "El ticket #{$ticketId} cambió a: {$newStatus}",
            ['type' => 'status_changed', 'ticket_id' => (string) $ticketId, 'status' => $newStatus]
        );
    }

    protected function getAndroidConfig(): AndroidConfig
    {
        return AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ]);
    }

    protected function getApnsConfig(): ApnsConfig
    {
        return ApnsConfig::fromArray([
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                    'badge' => 1,
                ],
            ],
        ]);
    }
}
