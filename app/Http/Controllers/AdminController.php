<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Constructor - Middleware para verificar autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Verificar si el usuario tiene permisos de administrador
     */
    private function checkAdminPermission()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'No tienes permisos para acceder al panel administrativo.');
        }
    }

    /**
     * Mostrar el dashboard administrativo
     */
    public function dashboard()
    {
        $this->checkAdminPermission();

        // Estadísticas generales actualizadas
        $stats = [
            'total_users' => User::count(),
            'total_tickets' => Ticket::count(),
            'pending_tickets' => Ticket::where('status', 'pendiente')->count(),
            'in_progress_tickets' => Ticket::where('status', 'en_progreso')->count(),
            'completed_tickets' => Ticket::where('status', 'finalizado')->count(),
            'total_surveys' => Survey::count(),
            'completed_surveys' => Survey::whereNotNull('completed_at')->count(),
            'pending_surveys' => Survey::whereNull('completed_at')->count(),
            'average_rating' => round(Survey::whereNotNull('completed_at')->avg('rating') ?? 0, 1),
        ];

        // Porcentaje de satisfacción general
        $completedSurveys = Survey::whereNotNull('completed_at')->get();
        $totalCompletedSurveys = $completedSurveys->count();
        
        if ($totalCompletedSurveys > 0) {
            // Consideramos satisfactorio si la calificación es 4 o 5
            $satisfiedSurveys = $completedSurveys->where('rating', '>=', 4)->count();
            $stats['satisfaction_percentage'] = round(($satisfiedSurveys / $totalCompletedSurveys) * 100, 1);
        } else {
            $stats['satisfaction_percentage'] = 0;
        }

        // Usuarios por rol
        $usersByRole = [
            'admin' => User::role('admin')->count(),
            'almacen' => User::role('almacen')->count(),
            'solicitante' => User::role('solicitante')->count(),
        ];

        // Estadísticas por miembro de almacén
        $almacenMembers = User::role('almacen')->get();
        $almacenStats = [];
        
        foreach ($almacenMembers as $member) {
            $memberTickets = Ticket::where('assigned_to', $member->id)->where('status', 'finalizado')->get();
            $memberSurveys = Survey::whereIn('ticket_id', $memberTickets->pluck('id'))
                                  ->whereNotNull('completed_at')
                                  ->get();
            
            $totalSurveys = $memberSurveys->count();
            $avgRating = $totalSurveys > 0 ? round($memberSurveys->avg('rating') ?? 0, 1) : 0;
            $satisfiedCount = $memberSurveys->where('rating', '>=', 4)->count();
            $satisfactionPercentage = $totalSurveys > 0 ? round(($satisfiedCount / $totalSurveys) * 100, 1) : 0;
            
            $almacenStats[] = [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'total_tickets' => Ticket::where('assigned_to', $member->id)->count(),
                'completed_tickets' => $memberTickets->count(),
                'total_surveys' => $totalSurveys,
                'average_rating' => $avgRating,
                'satisfaction_percentage' => $satisfactionPercentage,
            ];
        }

        // Ordenar por porcentaje de satisfacción descendente
        $almacenStats = collect($almacenStats)->sortByDesc('satisfaction_percentage')->values()->all();

        // Actividad reciente
        $recentUsers = User::with('roles')->orderBy('created_at', 'desc')->take(5)->get();
        $recentTickets = Ticket::with(['user', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Tickets paginados (ordenados del más actual primero)
        $allTickets = Ticket::with(['user', 'assignedTo', 'survey'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.dashboard-new', compact('stats', 'usersByRole', 'recentUsers', 'recentTickets', 'allTickets', 'almacenStats'));
    }

    /**
     * Listar todos los usuarios
     */
    public function users(Request $request)
    {
        $this->checkAdminPermission();

        $query = User::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = ['admin', 'almacen', 'solicitante'];

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function editUser(User $user)
    {
        $this->checkAdminPermission();

        $roles = ['admin', 'almacen', 'solicitante'];
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function updateUser(Request $request, User $user)
    {
        $this->checkAdminPermission();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|in:admin,almacen,solicitante',
        ]);

        // Actualizar datos básicos
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Sincronizar roles usando Spatie Permission
        $user->syncRoles($request->roles);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Asignar rol a usuario (AJAX)
     */
    public function assignRole(Request $request, User $user)
    {
        $this->checkAdminPermission();

        $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|in:admin,almacen,solicitante',
        ]);

        // Sincronizar roles usando Spatie Permission
        $user->syncRoles($request->roles);

        return response()->json([
            'success' => true,
            'message' => "Roles asignados exitosamente a {$user->name}",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'roles' => $user->getRolesString(),
            ]
        ]);
    }

    /**
     * Eliminar usuario
     */
    public function deleteUser(User $user)
    {
        $this->checkAdminPermission();

        // No permitir eliminar el propio usuario
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        // No permitir eliminar el único administrador
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->withErrors(['error' => 'No puedes eliminar el único administrador del sistema.']);
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario {$userName} eliminado exitosamente.");
    }

    /**
     * Estadísticas del sistema
     */
    public function statistics()
    {
        $this->checkAdminPermission();

        // Estadísticas avanzadas
        $stats = [
            'users' => [
                'total' => User::count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'by_role' => [
                    'admin' => User::role('admin')->count(),
                    'almacen' => User::role('almacen')->count(),
                    'solicitante' => User::role('solicitante')->count(),
                ]
            ],
            'tickets' => [
                'total' => Ticket::count(),
                'by_status' => [
                    'pendiente' => Ticket::where('status', 'pendiente')->count(),
                    'en_progreso' => Ticket::where('status', 'en_progreso')->count(),
                    'completado' => Ticket::where('status', 'completado')->count(),
                ],
                'this_month' => Ticket::whereMonth('created_at', now()->month)->count(),
                'completion_rate' => Ticket::count() > 0 ? 
                    round((Ticket::where('status', 'completado')->count() / Ticket::count()) * 100, 2) : 0,
            ],
            'surveys' => [
                'total' => Survey::count(),
                'completed' => Survey::whereNotNull('completed_at')->count(),
                'pending' => Survey::whereNull('completed_at')->count(),
                'average_rating' => Survey::whereNotNull('completed_at')->avg('rating') ?? 0,
                'completion_rate' => Survey::count() > 0 ? 
                    round((Survey::whereNotNull('completed_at')->count() / Survey::count()) * 100, 2) : 0,
            ]
        ];

        // Datos para gráficos (últimos 6 meses)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'users' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
                'tickets' => Ticket::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
                'surveys' => Survey::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
            ];
        }

        // Convertir a colección para poder usar métodos de colección en la vista
        $monthlyData = collect($monthlyData);

        return view('admin.statistics', compact('stats', 'monthlyData'));
    }
}
