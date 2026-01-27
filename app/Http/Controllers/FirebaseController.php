<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Exception\MessagingException;

class FirebaseController extends Controller
{
    protected $messaging;

    public function __construct()
    {
        // Inicializar Firebase solo cuando se use
    }

    /**
     * API de Prueba: Enviar notificación a un usuario (GET sin autenticación)
     * Ruta: GET /api/test-notification/{userId}
     * 
     * @param int $userId
     * @return JsonResponse
     */
    public function testNotification(int $userId): JsonResponse
    {
        try {
            // Obtener instancia de Messaging
            $this->messaging = app(Messaging::class);
            
            $topic = "user_{$userId}";
            $title = "Notificación de Prueba";
            $body = "Esta es una notificación de prueba enviada al usuario #{$userId}";

            $notification = Notification::create($title, $body);

            $messageData = [
                'topic' => $topic,
                'notification' => $notification,
                'data' => [
                    'type' => 'test',
                    'user_id' => (string) $userId,
                    'timestamp' => now()->toIso8601String()
                ]
            ];

            $androidConfig = AndroidConfig::fromArray([
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
            ]);

            $apnsConfig = ApnsConfig::fromArray([
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                        'badge' => 1,
                    ],
                ],
            ]);

            $message = CloudMessage::fromArray($messageData)
                ->withAndroidConfig($androidConfig)
                ->withApnsConfig($apnsConfig);

            $result = $this->messaging->send($message);

            Log::info("Notificación de prueba enviada", [
                'topic' => $topic,
                'user_id' => $userId,
                'result' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notificación de prueba enviada correctamente',
                'data' => [
                    'topic' => $topic,
                    'user_id' => $userId,
                    'message_id' => $result,
                    'title' => $title,
                    'body' => $body
                ]
            ], 200);

        } catch (MessagingException $e) {
            Log::error('Error al enviar notificación de prueba', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la notificación de prueba',
                'error' => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error inesperado al enviar notificación de prueba', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar notificación a un usuario específico
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sendNotificationToUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
            'image' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = $request->user_id;
            $topic = "user_{$userId}";
            $title = $request->title;
            $body = $request->body;
            $data = $request->data ?? [];
            $image = $request->image;

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

            $androidConfig = AndroidConfig::fromArray([
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
            ]);

            $apnsConfig = ApnsConfig::fromArray([
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                        'badge' => 1,
                    ],
                ],
            ]);

            $message = CloudMessage::fromArray($messageData)
                ->withAndroidConfig($androidConfig)
                ->withApnsConfig($apnsConfig);

            $result = $this->messaging->send($message);

            Log::info("Notificación enviada", [
                'topic' => $topic,
                'user_id' => $userId,
                'title' => $title
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notificación enviada correctamente',
                'data' => [
                    'topic' => $topic,
                    'user_id' => $userId,
                    'message_id' => $result
                ]
            ], 200);

        } catch (MessagingException $e) {
            Log::error('Error al enviar notificación', [
                'error' => $e->getMessage(),
                'user_id' => $request->user_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la notificación',
                'error' => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error inesperado al enviar notificación', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar notificación a múltiples usuarios
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sendNotificationToMultipleUsers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|integer',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
            'image' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userIds = $request->user_ids;
            $title = $request->title;
            $body = $request->body;
            $data = $request->data ?? [];
            $image = $request->image;

            $results = [];
            $successCount = 0;
            $failureCount = 0;

            foreach ($userIds as $userId) {
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

                    $androidConfig = AndroidConfig::fromArray([
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ]);

                    $apnsConfig = ApnsConfig::fromArray([
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ]);

                    $message = CloudMessage::fromArray($messageData)
                        ->withAndroidConfig($androidConfig)
                        ->withApnsConfig($apnsConfig);

                    $result = $this->messaging->send($message);

                    $results[] = [
                        'user_id' => $userId,
                        'topic' => $topic,
                        'success' => true,
                        'message_id' => $result
                    ];

                    $successCount++;

                } catch (\Exception $e) {
                    $results[] = [
                        'user_id' => $userId,
                        'topic' => "user_{$userId}",
                        'success' => false,
                        'error' => $e->getMessage()
                    ];

                    $failureCount++;

                    Log::error("Error al enviar notificación al usuario {$userId}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info("Notificaciones masivas enviadas", [
                'total' => count($userIds),
                'success' => $successCount,
                'failures' => $failureCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Notificaciones procesadas: {$successCount} exitosas, {$failureCount} fallidas",
                'data' => [
                    'total' => count($userIds),
                    'success_count' => $successCount,
                    'failure_count' => $failureCount,
                    'results' => $results
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al enviar notificaciones masivas', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
