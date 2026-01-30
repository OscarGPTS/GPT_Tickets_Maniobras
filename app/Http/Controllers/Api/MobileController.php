<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\Survey;
use App\Mail\TicketCompletedMail;
use App\Notifications\TicketCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MobileController extends Controller
{
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
            ->with(['user:id,name,email', 'solicitudImages:id,ticket_id,file_path', 'evidenciaImages:id,ticket_id,file_path'])
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
                    'uploaded_by' => $ticket->assigned_to,
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
}
