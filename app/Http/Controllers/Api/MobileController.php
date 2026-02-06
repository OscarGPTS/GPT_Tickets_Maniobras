<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\Survey;
use App\Mail\TicketCompletedMail;
use App\Notifications\TicketCompletedNotification;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketAssignedToWarehouseNotification;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MobileController extends Controller
{
    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }
    /**
     * Obtener tickets para usuarios de almacén
     * POST /api/mobile/tickets
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTickets(Request $request)
    {
        // Validar email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email inválido',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar usuario por email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado en el sistema'
            ], 404);
        }


        // Obtener tickets asignados al usuario (en_proceso y pendientes)
        $tickets = Ticket::whereIn('status', [Ticket::STATUS_EN_PROCESO, Ticket::STATUS_PENDIENTE])
            ->with(['user:id,name,email', 'assignedTo:id,name,email', 'solicitudImages:id,ticket_id,file_path', 'evidenciaImages:id,ticket_id,file_path'])
            ->orderByRaw("CASE WHEN status = 'en_proceso' THEN 0 ELSE 1 END")
            ->orderBy('assigned_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Formatear tickets para la respuesta
        $formattedTickets = $tickets->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'codigo' => $ticket->formatted_code,
                'titulo' => $ticket->title,
                'descripcion' => $ticket->description,
                'status' => $ticket->status,
                'status_texto' => $ticket->getStatusText(),
                'work_evidence' => $ticket->work_evidence,
                'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                'assigned_at' => $ticket->assigned_at?->format('Y-m-d H:i:s'),
                'completed_at' => $ticket->completed_at?->format('Y-m-d H:i:s'),
                'solicitante' => [
                    'id' => $ticket->user->id,
                    'nombre' => $ticket->user->name,
                    'email' => $ticket->user->email,
                ],
                'asignado_a' => $ticket->assignedTo ? [
                    'id' => $ticket->assignedTo->id,
                    'nombre' => $ticket->assignedTo->name,
                    'email' => $ticket->assignedTo->email,
                ] : null,
                'imagenes_solicitud' => $ticket->solicitudImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'path' => $image->file_path
                    ];
                }),
                'imagenes_evidencia' => $ticket->evidenciaImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'path' => $image->file_path
                    ];
                })
            ];
        });

        // Obtener información básica del usuario
        $userInfo = [
            'id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'rol' => $user->getRoleNames()->first(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Tickets obtenidos correctamente',
            'data' => [
                'usuario' => $userInfo,
                'tickets' => $formattedTickets,
                'estadisticas' => [
                    'total' => $tickets->count(),
                    'en_proceso' => $tickets->where('status', Ticket::STATUS_EN_PROCESO)->count(),
                    'pendientes' => $tickets->where('status', Ticket::STATUS_PENDIENTE)->count()
                ]
            ]
        ], 200);
    }

    /**
     * Completar ticket desde la app móvil
     * POST /api/mobile/tickets/complete
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeTicket(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'user_id' => 'required|exists:users,id',
            'descripcion' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar el ticket con la relación del usuario solicitante
        $ticket = Ticket::with('user')->find($request->ticket_id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);
        }

        // Buscar usuario que está completando el ticket
        $almacenUser = User::find($request->user_id);

        if (!$almacenUser) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Verificar que el usuario tenga rol de almacén o admin
        if (!$almacenUser->hasRole('almacen') && !$almacenUser->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Solo usuarios de almacén o administradores pueden completar tickets'
            ], 403);
        }

        // Verificar que el ticket esté en proceso
        if (!$ticket->isEnProceso()) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden completar tickets que estén en proceso'
            ], 400);
        }

        // Verificar que no esté cancelado
        if ($ticket->status === 'cancelado') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede completar un ticket cancelado'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Si el ticket no está asignado o está asignado a otro usuario, reasignar al usuario actual
            if ($ticket->assigned_to !== $almacenUser->id) {
                $ticket->assignTo($almacenUser);
                Log::info("Ticket #{$ticket->id} reasignado automáticamente al usuario #{$almacenUser->id} al completar");
            }

            // Actualizar ticket
            $ticket->update([
                'work_evidence' => $request->descripcion,
                'status' => Ticket::STATUS_FINALIZADO,
                'completed_at' => now(),
            ]);

            // Procesar imagen de evidencia
            if ($request->hasFile('imagen')) {
                $image = $request->file('imagen');
                $yearMonth = $ticket->created_at->format('Y/m');
                $path = $image->store('tickets/' . $yearMonth . '/' . $ticket->id . '/evidence', 'public');
                
                TicketImage::create([
                    'ticket_id' => $ticket->id,
                    'uploaded_by' => $almacenUser->id,
                    'file_path' => $path,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'file_size' => $image->getSize(),
                    'type' => TicketImage::TYPE_EVIDENCIA,
                ]);
            }

            // Crear encuesta de satisfacción (pendiente de completar por el solicitante)
            Survey::create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->user_id,
                'rating' => 0,
                'comments' => null,
            ]);

            // Notificar al usuario solicitante
            try {
                // Enviar correo al solicitante
                Mail::to($ticket->user->email)
                    ->send(new TicketCompletedMail($ticket, $ticket->user));
                
                // Enviar notificación en la base de datos
                $ticket->user->notify(new TicketCompletedNotification($ticket));
                
                // Enviar notificación push FCM
                $fcmResult = $this->fcmService->notifyTicketCompleted(
                    $ticket->user_id,
                    $ticket->id,
                    $ticket->title
                );
                
                if ($fcmResult['success']) {
                    Log::info('Notificación FCM de ticket completado enviada al usuario #' . $ticket->user_id);
                }
                
                Log::info('Notificación de ticket completado enviada desde API móvil al usuario #' . $ticket->user_id . ' para ticket #' . $ticket->id);
            } catch (\Exception $e) {
                // No fallar el proceso si el correo falla, solo registrar el error
                Log::error('Error al enviar notificación de ticket completado desde API: ' . $e->getMessage());
                // Continuar con el proceso aunque falle la notificación
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket completado exitosamente',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'codigo' => $ticket->formatted_code,
                    'status' => $ticket->status,
                    'completed_at' => $ticket->completed_at->format('Y-m-d H:i:s')
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al completar el ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener usuarios de almacén
     * GET /api/mobile/almacen-users
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAlmacenUsers()
    {
        try {
            $almacenUsers = User::role('almacen')
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            return response()->json([
                'success' => true,
                'message' => 'Usuarios de almacén obtenidos correctamente',
                'data' => [
                    'usuarios' => $almacenUsers->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'nombre' => $user->name,
                            'email' => $user->email
                        ];
                    })
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar ticket a usuario de almacén
     * POST /api/mobile/tickets/assign
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignTicket(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar el ticket con relaciones
        $ticket = Ticket::with(['user', 'assignedTo'])->find($request->ticket_id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);
        }

        // Verificar que el ticket no esté cancelado
        if ($ticket->status === 'cancelado') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede asignar un ticket cancelado'
            ], 400);
        }

        // Verificar que el ticket no esté finalizado
        if ($ticket->status === 'finalizado') {
            return response()->json([
                'success' => false,
                'message' => 'Este ticket ya ha sido finalizado'
            ], 400);
        }

        // Buscar usuario a asignar
        $assignedUser = User::find($request->assigned_to);

        // Verificar que el usuario tenga rol de almacén
        if (!$assignedUser->hasRole('almacen')) {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes asignar tickets a personal de almacén'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Asignar ticket
            $ticket->assignTo($assignedUser);

            // Notificar al usuario solicitante
            try {
                $ticket->user->notify(new TicketAssignedNotification($ticket));
                Log::info('Notificación de asignación enviada al usuario #' . $ticket->user_id . ' para ticket #' . $ticket->id);
            } catch (\Exception $e) {
                Log::error('Error al enviar notificación de ticket asignado: ' . $e->getMessage());
            }

            // Notificar a la persona asignada de almacén
            try {
                $assignedUser->notify(new TicketAssignedToWarehouseNotification($ticket));
                
                // Enviar notificación push FCM al usuario asignado
                $fcmResult = $this->fcmService->notifyTicketAssigned(
                    $assignedUser->id,
                    $ticket->id,
                    $ticket->title
                );
                
                if ($fcmResult['success']) {
                    Log::info('Notificación FCM de asignación enviada al almacén #' . $assignedUser->id);
                }
                
                Log::info('Notificación de asignación enviada al miembro de almacén #' . $assignedUser->id . ' para ticket #' . $ticket->id);
            } catch (\Exception $e) {
                Log::error('Error al enviar notificación al miembro de almacén asignado: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket asignado exitosamente a ' . $assignedUser->name,
                'data' => [
                    'ticket_id' => $ticket->id,
                    'codigo' => $ticket->formatted_code,
                    'status' => $ticket->status,
                    'assigned_at' => $ticket->assigned_at->format('Y-m-d H:i:s'),
                    'asignado_a' => [
                        'id' => $assignedUser->id,
                        'nombre' => $assignedUser->name,
                        'email' => $assignedUser->email
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tickets finalizados según el rol del usuario (paginado y filtrado por mes/año)
     * POST /api/mobile/tickets/completed
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompletedTickets(Request $request)
    {
        // Validar datos de entrada
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'mes' => 'nullable|integer|min:1|max:12',
            'anio' => 'nullable|integer|min:2020|max:2100',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:5|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar usuario
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado en el sistema'
            ], 404);
        }

        // Determinar mes y año (si no se proporciona, usar actual)
        $mes = $request->mes ?? now()->month;
        $anio = $request->anio ?? now()->year;
        $perPage = $request->per_page ?? 15;

        // Construir query base: solo tickets finalizados
        $query = Ticket::where('status', Ticket::STATUS_FINALIZADO)
            ->whereMonth('completed_at', $mes)
            ->whereYear('completed_at', $anio)
            ->with([
                'user:id,name,email',
                'assignedTo:id,name,email',
                'solicitudImages:id,ticket_id,file_path',
                'evidenciaImages:id,ticket_id,file_path',
                'survey'
            ]);

        // Filtrar según el rol del usuario
        if ($user->hasRole('admin')) {
            // Admin: ver TODAS las solicitudes finalizadas
            // No se aplica filtro adicional
        } elseif ($user->hasRole('almacen')) {
            // Almacén: solo tickets asignados a él
            $query->where('assigned_to', $user->id);
        } elseif ($user->hasRole('solicitante')) {
            // Solicitante: solo tickets creados por él
            $query->where('user_id', $user->id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene un rol válido asignado'
            ], 403);
        }

        // Obtener tickets paginados ordenados por fecha de finalización (más recientes primero)
        $tickets = $query->orderBy('completed_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Formatear tickets para la respuesta
        $formattedTickets = $tickets->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'codigo' => $ticket->formatted_code,
                'titulo' => $ticket->title,
                'descripcion' => $ticket->description,
                'status' => $ticket->status,
                'status_texto' => $ticket->getStatusText(),
                'work_evidence' => $ticket->work_evidence,
                'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                'assigned_at' => $ticket->assigned_at?->format('Y-m-d H:i:s'),
                'completed_at' => $ticket->completed_at?->format('Y-m-d H:i:s'),
                'solicitante' => [
                    'id' => $ticket->user->id,
                    'nombre' => $ticket->user->name,
                    'email' => $ticket->user->email,
                ],
                'asignado_a' => $ticket->assignedTo ? [
                    'id' => $ticket->assignedTo->id,
                    'nombre' => $ticket->assignedTo->name,
                    'email' => $ticket->assignedTo->email,
                ] : null,
                'imagenes_solicitud' => $ticket->solicitudImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'path' => $image->file_path
                    ];
                }),
                'imagenes_evidencia' => $ticket->evidenciaImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'path' => $image->file_path
                    ];
                }),
                'encuesta' => $ticket->survey ? [
                    'id' => $ticket->survey->id,
                    'rating' => $ticket->survey->rating,
                    'comentarios' => $ticket->survey->comments,
                    'completada' => $ticket->survey->completed_at ? true : false,
                    'completed_at' => $ticket->survey->completed_at?->format('Y-m-d H:i:s'),
                ] : null,
            ];
        });

        // Calcular estadísticas del mes actual (sin paginación)
        $queryStats = clone $query;
        $allTicketsOfMonth = $queryStats->get();

        // Obtener información básica del usuario
        $userInfo = [
            'id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'rol' => $user->getRoleNames()->first(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Tickets finalizados obtenidos correctamente',
            'data' => [
                'usuario' => $userInfo,
                'filtro' => [
                    'mes' => $mes,
                    'anio' => $anio,
                    'mes_nombre' => \Carbon\Carbon::create($anio, $mes)->locale('es')->translatedFormat('F Y'),
                ],
                'tickets' => $formattedTickets,
                'paginacion' => [
                    'total' => $tickets->total(),
                    'por_pagina' => $tickets->perPage(),
                    'pagina_actual' => $tickets->currentPage(),
                    'ultima_pagina' => $tickets->lastPage(),
                    'desde' => $tickets->firstItem(),
                    'hasta' => $tickets->lastItem(),
                    'tiene_mas_paginas' => $tickets->hasMorePages(),
                ],
                'estadisticas' => [
                    'total_finalizados' => $allTicketsOfMonth->count(),
                    'con_encuesta_completada' => $allTicketsOfMonth->filter(function ($ticket) {
                        return $ticket->survey && $ticket->survey->completed_at;
                    })->count(),
                    'promedio_calificacion' => round($allTicketsOfMonth->filter(function ($ticket) {
                        return $ticket->survey && $ticket->survey->completed_at && $ticket->survey->rating > 0;
                    })->avg(function ($ticket) {
                        return $ticket->survey->rating;
                    }) ?? 0, 1),
                ]
            ]
        ], 200);
    }

    /**
     * Crear nuevo ticket desde la app móvil (Solicitante)
     * POST /api/mobile/tickets/create
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTicket(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagenes' => 'nullable|array|max:5',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar usuario
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Verificar que el usuario tenga rol de solicitante
        if (!$user->hasRole('solicitante')) {
            return response()->json([
                'success' => false,
                'message' => 'Solo usuarios con rol de solicitante pueden crear tickets'
            ], 403);
        }

        // Verificar si el usuario puede crear un ticket (no debe tener encuestas pendientes)
        if (!$user->canCreateTicket()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes completar las encuestas pendientes antes de crear un nuevo ticket',
                'encuestas_pendientes' => true
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Crear el ticket
            $ticket = Ticket::create([
                'user_id' => $user->id,
                'title' => $request->titulo,
                'description' => $request->descripcion,
                'status' => Ticket::STATUS_PENDIENTE,
            ]);

            // Procesar imágenes si las hay
            $imagenesGuardadas = [];
            if ($request->hasFile('imagenes')) {
                $yearMonth = now()->format('Y/m');
                foreach ($request->file('imagenes') as $image) {
                    $path = $image->store('tickets/' . $yearMonth . '/' . $ticket->id . '/solicitud', 'public');
                    
                    $ticketImage = TicketImage::create([
                        'ticket_id' => $ticket->id,
                        'uploaded_by' => $user->id,
                        'file_path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                        'type' => TicketImage::TYPE_SOLICITUD,
                    ]);

                    $imagenesGuardadas[] = [
                        'id' => $ticketImage->id,
                        'path' => $ticketImage->file_path
                    ];
                }
            }

            // Notificar a los administradores
            try {
                $admins = User::role('admin')->get();
                if ($admins->count() > 0) {
                    // Notificación en base de datos
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\TicketPendingApprovalNotification($ticket));
                    
                    // Notificación push FCM
                    $fcmResult = $this->fcmService->notifyNewTicketToAdmins(
                        $ticket->id,
                        $ticket->title,
                        $user->name
                    );
                    
                    if ($fcmResult['success']) {
                        Log::info('Notificación FCM enviada a ' . ($fcmResult['success_count'] ?? 0) . ' admins desde API móvil');
                    }
                    
                    Log::info('Ticket #' . $ticket->id . ' creado desde API móvil por usuario #' . $user->id . '. Notificados ' . $admins->count() . ' admins.');
                }
            } catch (\Exception $e) {
                // No fallar el proceso si las notificaciones fallan
                Log::error('Error al enviar notificaciones de ticket creado desde API: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket creado exitosamente. Se ha notificado al equipo de almacén.',
                'data' => [
                    'ticket' => [
                        'id' => $ticket->id,
                        'codigo' => $ticket->formatted_code,
                        'titulo' => $ticket->title,
                        'descripcion' => $ticket->description,
                        'status' => $ticket->status,
                        'status_texto' => $ticket->getStatusText(),
                        'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                        'solicitante' => [
                            'id' => $user->id,
                            'nombre' => $user->name,
                            'email' => $user->email,
                        ],
                        'imagenes' => $imagenesGuardadas
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el ticket: ' . $e->getMessage()
            ], 500);
        }
    }
}
