<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Ticket;
use App\Notifications\SurveyCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SurveyController extends Controller
{
    /**
     * Display a listing of user's surveys.
     */
    public function index()
    {
        $user = Auth::user();
        
        $surveys = $user->surveys()
            ->with(['ticket.assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Estadísticas para el dashboard
        $stats = [
            'total' => $user->surveys()->count(),
            'completed' => $user->surveys()->whereNotNull('completed_at')->count(),
            'pending' => $user->surveys()->whereNull('completed_at')->count(),
            'average_rating' => $user->surveys()->whereNotNull('completed_at')->avg('rating'),
        ];

        return view('surveys.index', compact('surveys', 'stats'));
    }

    /**
     * Show the form for creating a new survey.
     */
    public function create()
    {
        // No se usa, las encuestas se crean automáticamente
        abort(404);
    }

    /**
     * Store a newly created survey in storage.
     */
    public function store(Request $request)
    {
        // No se usa, las encuestas se crean automáticamente
        abort(404);
    }

    /**
     * Display the specified survey.
     */
    public function show(Survey $survey)
    {
        // Verificar que el usuario puede ver esta encuesta
        if ($survey->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta encuesta.');
        }

        $survey->load(['ticket.assignedTo', 'ticket.images']);

        return view('surveys.show', compact('survey'));
    }

    /**
     * Show the form for editing the specified survey.
     */
    public function edit(Survey $survey)
    {
        // Verificar que el usuario puede editar esta encuesta
        if ($survey->user_id !== Auth::id() || $survey->isCompleted()) {
            abort(403, 'No puedes editar esta encuesta.');
        }

        $survey->load(['ticket.assignedTo', 'ticket.images']);

        return view('surveys.edit', compact('survey'));
    }

    /**
     * Update the specified survey in storage.
     */
    public function update(Request $request, Survey $survey)
    {
        // Verificar que el usuario puede editar esta encuesta
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

            return redirect()->route('surveys.index')
                ->with('success', 'Encuesta completada exitosamente. ¡Gracias por tu feedback!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al completar la encuesta: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified survey from storage.
     */
    public function destroy(Survey $survey)
    {
        // Las encuestas no se pueden eliminar
        abort(403, 'Las encuestas no se pueden eliminar.');
    }

    /**
     * Get pending surveys for the authenticated user
     */
    public function pending()
    {
        $pendingSurveys = Auth::user()->surveys()
            ->pendientes()
            ->with(['ticket.assignedTo', 'ticket.images'])
            ->get();

        return view('surveys.pending', compact('pendingSurveys'));
    }

    /**
     * Simple completion of survey (simplified process)
     */
    public function completeSimple(Request $request, Survey $survey)
    {
        // Verificar que el usuario puede completar esta encuesta
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

            // Notificar al miembro de almacén que completó el ticket
            $ticket = $survey->ticket;
            if ($ticket->assignedTo) {
                try {
                    $ticket->assignedTo->notify(new SurveyCompletedNotification($survey));
                    Log::info('Notificación de encuesta completada enviada al usuario #' . $ticket->assigned_to . ' para ticket #' . $ticket->id);
                } catch (\Exception $e) {
                    Log::error('Error al enviar notificación de encuesta completada: ' . $e->getMessage());
                    // No detenemos el proceso si falla la notificación
                }
            }

            DB::commit();

            return redirect()->route('tickets.show', $survey->ticket_id)
                ->with('success', '¡Gracias por tu calificación!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al completar la encuesta: ' . $e->getMessage()]);
        }
    }

    /**
     * Quick completion of survey with 5 stars
     */
    public function quickComplete(Request $request, Survey $survey)
    {
        // Verificar que el usuario puede completar esta encuesta
        if ($survey->user_id !== Auth::id() || $survey->isCompleted()) {
            abort(403, 'No puedes completar esta encuesta.');
        }

        try {
            $survey->update([
                'rating' => 5,
                'feedback' => 'Completado con calificación rápida de 5 estrellas.',
                'completed_at' => now(),
            ]);

            return redirect()->route('surveys.show', $survey)
                ->with('success', 'Encuesta completada con 5 estrellas. ¡Gracias por tu feedback!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al completar la encuesta: ' . $e->getMessage()]);
        }
    }

    /**
     * Quick completion of all pending surveys with 5 stars
     */
    public function quickCompleteAll(Request $request)
    {
        try {
            $pendingSurveys = Auth::user()->surveys()
                ->pendientes()
                ->get();

            if ($pendingSurveys->isEmpty()) {
                return redirect()->route('surveys.pending')
                    ->with('error', 'No tienes encuestas pendientes para completar.');
            }

            $completed = 0;
            foreach ($pendingSurveys as $survey) {
                $survey->update([
                    'rating' => 5,
                    'feedback' => 'Completado masivamente con calificación rápida de 5 estrellas.',
                    'completed_at' => now(),
                ]);
                $completed++;
            }

            return redirect()->route('surveys.index')
                ->with('success', "Se completaron {$completed} encuestas con 5 estrellas. ¡Gracias por tu feedback!");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al completar las encuestas: ' . $e->getMessage()]);
        }
    }
}
