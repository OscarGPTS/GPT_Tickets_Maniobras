<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo y Navegación Principal -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('storage/img/logo.png') }}" alt="Logo" style="width: 60px; height: auto;">
                        <span class="ml-3 text-xl font-bold bg-gradient-to-r bg-red-600  bg-clip-text text-transparent">
                            Sistema de Tickets de Cargas
                        </span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:block ml-10">
                    <div class="flex items-center space-x-1">
                        @auth
                            <a href="{{ route('home') }}" 
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('home') || request()->routeIs('solicitante.dashboard') || request()->routeIs('almacen.dashboard') || request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600' }}">
                                <i class="fas fa-home mr-2 {{ request()->routeIs('home') || request()->routeIs('*.dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}"></i>
                                Dashboard
                            </a>
                            
                            @if(auth()->user()->hasRole('solicitante'))
                                <a href="{{ route('solicitante.tickets.index') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('solicitante.tickets.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-ticket-alt mr-2 {{ request()->routeIs('solicitante.tickets.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}"></i>
                                    Mis Tickets
                                </a>
                            @endif
                            
                            @if(auth()->user()->roles()->where('name', 'almacen')->exists())
                                <a href="{{ route('almacen.dashboard') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('almacen.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-warehouse mr-2 {{ request()->routeIs('almacen.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}"></i>
                                    Panel Almacén
                                </a>
                            @endif
                            
                            @if(auth()->user()->roles()->where('name', 'admin')->exists())
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-cog mr-2 {{ request()->routeIs('admin.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}"></i>
                                    Administración
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                @auth
                    <!-- Notifications -->
                    @php
                        $notificationsRoute = 'notifications.index';
                        if(auth()->user()->hasRole('solicitante')) {
                            $notificationsRoute = 'solicitante.notifications.index';
                        } elseif(auth()->user()->hasRole('almacen')) {
                            $notificationsRoute = 'almacen.notifications.index';
                        } elseif(auth()->user()->hasRole('admin')) {
                            $notificationsRoute = 'admin.notifications.index';
                        }
                    @endphp
                    <a href="{{ route($notificationsRoute) }}" 
                       class="relative p-2 text-gray-400 hover:text-indigo-600 transition-colors duration-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-bell text-lg"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold border-2 border-white">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" 
                                type="button" 
                                class="flex items-center space-x-3 focus:outline-none group">
                            <!-- Avatar -->
                            <div class="relative">
                                @if(auth()->user()->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white group-hover:ring-indigo-500 transition-all duration-200" 
                                         src="{{ auth()->user()->getAvatarUrl() }}" 
                                         alt="{{ auth()->user()->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center ring-2 ring-white group-hover:ring-indigo-500 transition-all duration-200">
                                        <span class="text-white font-semibold text-sm">{{ auth()->user()->getInitials() }}</span>
                                    </div>
                                @endif
                                <div class="absolute bottom-0 right-0 h-3 w-3 bg-green-400 rounded-full border-2 border-white"></div>
                            </div>
                            
                            <!-- User Info (Hidden on mobile) -->
                            <div class="hidden lg:block text-left">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Usuario' }}
                                </p>
                            </div>
                            
                            <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-indigo-600 transition-colors hidden lg:block" 
                               :class="{ 'rotate-180': open }"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-50 mt-3 w-72 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                             role="menu">
                            
                            <!-- User Info Header -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    @if(auth()->user()->avatar)
                                        <img class="h-12 w-12 rounded-full object-cover" 
                                             src="{{ auth()->user()->getAvatarUrl() }}" 
                                             alt="{{ auth()->user()->name }}">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                            <span class="text-white font-semibold">{{ auth()->user()->getInitials() }}</span>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ auth()->user()->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ auth()->user()->email }}
                                        </p>
                                        <div class="flex items-center mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                <i class="fas fa-user-tag mr-1"></i>
                                                {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Usuario' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-2">
                                @php
                                    $notificationsRoute = 'notifications.index';
                                    if(auth()->user()->hasRole('solicitante')) {
                                        $notificationsRoute = 'solicitante.notifications.index';
                                    } elseif(auth()->user()->hasRole('almacen')) {
                                        $notificationsRoute = 'almacen.notifications.index';
                                    } elseif(auth()->user()->hasRole('admin')) {
                                        $notificationsRoute = 'admin.notifications.index';
                                    }
                                @endphp
                                <a href="{{ route($notificationsRoute) }}" 
                                   class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                   role="menuitem">
                                    <i class="fas fa-bell mr-3 text-gray-400 group-hover:text-indigo-500"></i>
                                    <span class="flex-1">Notificaciones</span>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">
                                            {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </a>
                                
                                <a href="{{ route('home') }}" 
                                   class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                   role="menuitem">
                                    <i class="fas fa-user-circle mr-3 text-gray-400 group-hover:text-indigo-500"></i>
                                    Mi Perfil
                                </a>
                                
                                @if(auth()->user()->hasRole('solicitante'))
                                    <a href="{{ route('solicitante.tickets.index') }}" 
                                       class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                       role="menuitem">
                                        <i class="fas fa-ticket-alt mr-3 text-gray-400 group-hover:text-indigo-500"></i>
                                        Mis Tickets
                                    </a>
                                @endif
                            </div>

                            <!-- Logout -->
                            <div class="border-t border-gray-100 py-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="group flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors"
                                            role="menuitem">
                                        <i class="fas fa-sign-out-alt mr-3 text-red-400 group-hover:text-red-600"></i>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
