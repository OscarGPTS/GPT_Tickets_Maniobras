<?php

namespace App\Http\Controllers\Solicitante;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\User;
use App\Notifications\SurveyCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SurveyController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listar encuestas del usuario
     */
    public function index()
    {
        $user = Auth::user();
        
        $surveys = $user->surveys()
            ->with(['ticket.assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Estadísticas
        $stats = [
            'total' => $user->surveys()->count(),
            'completed' => $user->surveys()->whereNotNull('completed_at')->count(),
            'pending' => $user->surveys()->whereNull('completed_at')->count(),
            'average_rating' => $user->surveys()->whereNotNull('completed_at')->avg('rating'),
        ];

        return view('solicitante.surveys.index', compact('surveys', 'stats'));
    }

    /**
     * Ver encuesta
     */
    public function show(Survey $survey)
    {
        // Verificar permisos
        if ($survey->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta encuesta.');
        }

        $survey->load(['ticket.assignedTo', 'ticket.images']);

        return view('solicitante.surveys.show', compact('survey'));
    }

    /**
     * Editar encuesta pendiente
     */
    public function edit(Survey $survey)
    {
        // Verificar permisos
        if ($survey->user_id !== Auth::id() || $survey->isCompleted()) {
            abort(403, 'No puedes editar esta encuesta.');
        }

        $survey->load(['ticket.assignedTo', 'ticket.images']);

        return view('solicitante.surveys.edit', compact('survey'));
    }

    /**
     * Actualizar encuesta
     */
    public function update(Request $request, Survey $survey)
    {
        // Verificar permisos
        if ($survey->user_id !== Auth::id() || $survey->isCompleted()) {
            abort(403, 'No puedes editar esta encuesta.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        try {
            $survey->update([
                'rating' => $request->rating,
                'feedback' => $request->feedback,
                'completed_at' => now(),
            ]);

            return redirect()->route('solicitante.surveys.index')
                ->with('success', 'Encuesta completada exitosamente. ¡Gracias por tu feedback!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al completar la encuesta: ' . $e->getMessage()]);
        }
    }

    /**
     * Encuestas pendientes
     */
    public function pending()
    {
        $pendingSurveys = Auth::user()->surveys()
            ->whereNull('completed_at')
            ->with(['ticket.assignedTo', 'ticket.images'])
            ->get();

        return view('solicitante.surveys.pending', compact('pendingSurveys'));
    }

    /**
     * Completar encuesta simple
     */
    public function complete(Request $request, Survey $survey)
    {
        // Verificar permisos
        if ($survey->user_id !== Auth::id() || $survey->isCompleted()) {
            abort(403, 'No puedes completar esta encuesta.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $survey->update([
                'rating' => $request->rating,
                'comments' => $request->comments,
                'completed_at' => now(),
            ]);

            // Notificar a todos los admins
            $ticket = $survey->ticket;
            try {
                $admins = User::role('admin')->get();
                if ($admins->count() > 0) {
                    Notification::send($admins, new SurveyCompletedNotification($survey));
                    Log::info('Notificación de encuesta completada enviada a ' . $admins->count() . ' admins para ticket #' . $ticket->id);
                }
            } catch (\Exception $e) {
                Log::error('Error al enviar notificación de encuesta completada: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('solicitante.tickets.show', $survey->ticket_id)
                ->with('success', '¡Gracias por tu calificación!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al completar la encuesta: ' . $e->getMessage()]);
        }
    }

    /**
     * Completar rápido con 5 estrellas
     */
    public function quickComplete(Request $request, Survey $survey)
    {
        // Verificar permisos
        if ($survey->user_id !== Auth::id() || $survey->isCompleted()) {
            abort(403, 'No puedes completar esta encuesta.');
        }

        try {
            $survey->update([
                'rating' => 5,
                'feedback' => 'Completado con calificación rápida de 5 estrellas.',
                'completed_at' => now(),
            ]);

            return redirect()->route('solicitante.surveys.show', $survey)
                ->with('success', 'Encuesta completada con 5 estrellas. ¡Gracias!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al completar la encuesta: ' . $e->getMessage()]);
        }
    }

    /**
     * Completar todas pendientes con 5 estrellas
     */
    public function quickCompleteAll(Request $request)
    {
        try {
            $pendingSurveys = Auth::user()->surveys()
                ->whereNull('completed_at')
                ->get();

            if ($pendingSurveys->isEmpty()) {
                return redirect()->route('solicitante.surveys.pending')
                    ->with('error', 'No tienes encuestas pendientes.');
            }

            $completed = 0;
            foreach ($pendingSurveys as $survey) {
                $survey->update([
                    'rating' => 5,
                    'feedback' => 'Completado masivamente con 5 estrellas.',
                    'completed_at' => now(),
                ]);
                $completed++;
            }

            return redirect()->route('solicitante.surveys.index')
                ->with('success', "Se completaron {$completed} encuestas con 5 estrellas. ¡Gracias!");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al completar las encuestas: ' . $e->getMessage()]);
        }
    }
}
