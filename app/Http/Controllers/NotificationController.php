<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(15);

        // NO marcar automáticamente como leídas al visualizar la lista
        // El usuario debe marcarlas manualmente

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read and redirect to its action URL.
     */
    public function show($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        // Marcar como leída
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        // Redirigir a la URL de acción si existe
        if (isset($notification->data['action_url'])) {
            return redirect($notification->data['action_url']);
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return back()->with('success', 'Notificación marcada como leída.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas las notificaciones han sido marcadas como leídas.');
    }

    /**
     * Delete a specific notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return back()->with('success', 'Notificación eliminada exitosamente.');
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead()
    {
        Auth::user()
            ->readNotifications()
            ->delete();

        return back()->with('success', 'Todas las notificaciones leídas han sido eliminadas.');
    }
}
