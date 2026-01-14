@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notificaciones</h1>
            <p class="mt-1 text-sm text-gray-500">
                Mantente al día con todas tus actualizaciones
            </p>
        </div>
        
        @if(auth()->user()->unreadNotifications->count() > 0 || auth()->user()->readNotifications->count() > 0)
            <div class="flex items-center space-x-2">
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-check-double mr-2"></i>
                            Marcar todas como leídas
                        </button>
                    </form>
                @endif
                
                @if(auth()->user()->readNotifications->count() > 0)
                    <form method="POST" action="{{ route('notifications.delete-all-read') }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('¿Estás seguro de eliminar todas las notificaciones leídas?')"
                                class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i>
                            Eliminar leídas
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <!-- Notificaciones -->
    @if($notifications->count() > 0)
        <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
            @foreach($notifications as $notification)
                <div class="p-4 hover:bg-gray-50 transition-colors {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50' }}">
                    <div class="flex items-start space-x-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center
                                {{ $notification->data['color'] === 'blue' ? 'bg-blue-100 text-blue-600' : '' }}
                                {{ $notification->data['color'] === 'green' ? 'bg-green-100 text-green-600' : '' }}
                                {{ $notification->data['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-600' : '' }}
                                {{ $notification->data['color'] === 'indigo' ? 'bg-indigo-100 text-indigo-600' : '' }}
                                {{ $notification->data['color'] === 'red' ? 'bg-red-100 text-red-600' : '' }}">
                                <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                
                                @if(!$notification->read_at)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Nueva
                                    </span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="mt-3 flex items-center space-x-3">
                                @if(isset($notification->data['action_url']))
                                    <a href="{{ route('notifications.show', $notification->id) }}" 
                                       class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        Ver detalles
                                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                    </a>
                                @endif
                                
                                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-sm font-medium text-red-600 hover:text-red-900"
                                            onclick="return confirm('¿Eliminar esta notificación?')">
                                        <i class="fas fa-trash mr-1"></i>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white shadow rounded-lg p-12 text-center">
            <div class="mx-auto h-24 w-24 text-gray-400">
                <i class="fas fa-bell-slash text-6xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No tienes notificaciones</h3>
            <p class="mt-2 text-sm text-gray-500">
                Cuando recibas notificaciones, aparecerán aquí.
            </p>
            <div class="mt-6">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-home mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
