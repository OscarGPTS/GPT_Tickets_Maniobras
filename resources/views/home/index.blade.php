@extends('layouts.app')

@section('content')

<style>
/* Estilos personalizados para el dashboard */
.dashboard-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.dashboard-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(220, 38, 38, 0.15);
}

.app-card {
    background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.app-card:hover {
    background: linear-gradient(135deg, #B91C1C 0%, #F59E0B 100%);
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 25px 50px rgba(220, 38, 38, 0.25);
}

.coming-soon-card {
    background: linear-gradient(135deg, #9CA3AF 0%, #D1D5DB 100%);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.coming-soon-card:hover {
    background: linear-gradient(135deg, #6B7280 0%, #9CA3AF 100%);
    transform: translateY(-5px);
}

.gradient-text {
    background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.glass-effect {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.fade-in {
    animation: fadeInUp 0.8s ease-out forwards;
}

.fade-in-delay-1 { animation-delay: 0.1s; opacity: 0; }
.fade-in-delay-2 { animation-delay: 0.2s; opacity: 0; }
.fade-in-delay-3 { animation-delay: 0.3s; opacity: 0; }
.fade-in-delay-4 { animation-delay: 0.4s; opacity: 0; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pulse-slow {
    animation: pulseGlow 3s ease-in-out infinite;
}

@keyframes pulseGlow {
    0%, 100% { 
        box-shadow: 0 0 20px rgba(220, 38, 38, 0.3);
    }
    50% { 
        box-shadow: 0 0 30px rgba(251, 191, 36, 0.4);
    }
}

.section-header {
    position: relative;
    overflow: hidden;
}

.section-header::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
    transform: translateY(-50%);
    border-radius: 2px;
}
</style>

<!-- Header principal con animación -->
<div class="px-4 pt-6 pb-4">
    <div class="text-center mb-8 fade-in">
        <h1 class="text-4xl md:text-5xl font-bold gradient-text mb-4">
            🚀 Explorador IA
        </h1>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">
            Tu centro de comando inteligente para explorar, analizar y descubrir información de manera eficiente
        </p>
    </div>
</div>

<div class="px-4 pb-8">
    <div class="grid grid-cols-1 lg:grid-cols-[65%_35%] gap-8">
        
        <!-- Sección principal de aplicaciones -->
        <div class="dashboard-card glass-effect rounded-3xl p-8 shadow-xl fade-in fade-in-delay-1">
            <div class="section-header flex items-center mb-8 pl-6">
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-800">💼 Mis Aplicaciones</h2>
                    <p class="text-gray-500 text-sm mt-1">Herramientas inteligentes a tu alcance</p>
                </div>
            </div>

            <!-- Grid de aplicaciones -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                
                <!-- Buscador Inteligente -->
                <a href="{{ route('chat.index') }}" class="block fade-in fade-in-delay-2">
                    <div class="app-card rounded-2xl flex flex-col items-center justify-center p-6 text-center 
                               min-h-[200px] relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent 
                                   opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex justify-center items-center flex-col">
                            <div class="bg-white/20 rounded-full p-4 mb-4 group-hover:scale-110 transition-transform duration-300 " style="width: 100px;">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.847a4.5 4.5 0 003.09 3.09L15.75 12l-2.847.813a4.5 4.5 0 00-3.09 3.091zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                </svg>
                            </div>
                            <h3 class="text-white font-bold text-lg mb-2">Buscador Inteligente</h3>
                            <p class="text-white/80 text-sm">Encuentra información corporativa instantáneamente</p>
                        </div>
                    </div>
                </a>



                <a href="{{ route('recommendations.index') }}" class="block fade-in fade-in-delay-2">
                    <div class="app-card rounded-2xl flex flex-col items-center justify-center p-6 text-center 
                               min-h-[200px] relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent 
                                   opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex justify-center items-center flex-col">
                            <div class="bg-white/20 rounded-full p-4 mb-4 group-hover:scale-110 transition-transform duration-300 " style="width: 100px;">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 00-3.75 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                                </svg>
                            </div>
                            <h3 class="text-white font-bold text-lg mb-2">Recomendaciones</h3>
                            <p class="text-white/80 text-sm">Sugerencias personalizadas e inteligentes</p>
                        </div>
                    </div>
                </a>

             

                <!-- Noticias -->
                <a href="{{ route('news.index') }}" class="block fade-in fade-in-delay-4">
                    <div class="app-card rounded-2xl flex flex-col items-center justify-center p-6 text-center 
                               min-h-[200px] relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent 
                                   opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex justify-center items-center flex-col">
                            <div class=" bg-white/20 rounded-full p-4 mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg fill="#FFFFFF" width="64px" height="64px" viewBox="-2 0 19 19" xmlns="http://www.w3.org/2000/svg" class="cf-icon-svg"><path d="M2.644 15.26a16.9 16.9 0 0 1-.706-.014l-.11-.025a1.51 1.51 0 0 1-1.14-1.185l-.018-.092c-.005-.106-.01-.406-.01-.667V4.434a.477.477 0 0 1 .476-.475H11.77a.476.476 0 0 1 .475.475v1.529h1.591a.506.506 0 0 1 .504.504v7.192a1.6 1.6 0 0 1-1.6 1.6zm0-1.109h8.572a1.598 1.598 0 0 1-.077-.491v-2.174a2.16 2.16 0 0 1-.003-.109v-6.31H1.769v8.21c0 .218.003.43.006.544l.002.008a.401.401 0 0 0 .3.312l.01.002c.133.004.358.008.557.008zM9.91 6.815H2.95v1.109h6.96zm-4 2.383H2.95v3.532h2.96zm4.002.026H7.033v1.109h2.878zm0 2.41H7.033v1.108h2.878zm2.336-4.563v6.589a.492.492 0 0 0 .984 0V7.07z"/></svg>
                            </div>
                            <h3 class="text-white font-bold text-lg mb-2">Noticias</h3>
                            <p class="text-white/80 text-sm">Mantente al día con las últimas noticias</p>
                        </div>
                    </div>
                </a>

                <!-- Mi Perfil -->
                <a href="{{ route('profile.index') }}" class="block fade-in fade-in-delay-2">
                    <div class="app-card rounded-2xl flex flex-col items-center justify-center p-6 text-center 
                               min-h-[200px] relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent 
                                   opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex justify-center items-center flex-col">
                            <div class="bg-white/20 rounded-full p-4 mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="text-white font-bold text-lg mb-2">Mi Perfil</h3>
                            <p class="text-white/80 text-sm">Gestiona tu información personal</p>
                        </div>
                    </div>
                </a>

                <!-- Tarjetas de "Próximamente" mejoradas -->
                @for ($i = 0; $i < 5; $i++)
                <div class="coming-soon-card rounded-2xl flex flex-col items-center justify-center p-6 text-center 
                           min-h-[200px] relative overflow-hidden group cursor-not-allowed fade-in fade-in-delay-{{ ($i % 4) + 1 }}">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="relative z-10 flex justify-center items-center flex-col">
                        <div class="bg-white/20 rounded-full p-4 mb-4">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
	                            width="64px" height="64px" fill="#000000" viewBox="0 0 32 32" xml:space="preserve">

                                <g>
                                    <g>
                                        <path class="open_een" d="M15.977,29.993c-3.45,0.001-6.706-1.236-9.307-3.563C3.702,23.775,2,19.971,2,15.992
                                            C2,8.75,7.647,2.621,14.857,2.039c0.422-0.038,0.849,0.111,1.161,0.398C16.329,2.724,16.5,3.114,16.5,3.536v11.171
                                            c0,0.433,0.353,0.785,0.786,0.785h4.5c0.276,0,0.5,0.224,0.5,0.5s-0.224,0.5-0.5,0.5h-4.5c-0.984,0-1.786-0.801-1.786-1.785V3.536
                                            c0-0.14-0.057-0.269-0.16-0.363c-0.108-0.101-0.249-0.148-0.402-0.138C8.244,3.576,3,9.268,3,15.992
                                            c0,3.694,1.581,7.228,4.336,9.692c2.793,2.499,6.408,3.646,10.162,3.224c5.899-0.658,10.696-5.41,11.405-11.299
                                            c0.764-6.345-3.1-12.266-9.186-14.078c-0.265-0.079-0.416-0.357-0.336-0.622c0.079-0.264,0.356-0.412,0.622-0.337
                                            c6.555,1.953,10.716,8.327,9.894,15.156c-0.764,6.345-5.932,11.465-12.287,12.174C17.062,29.963,16.517,29.993,15.977,29.993z"/>
                                    </g>
                                    <g>
                                        <path class="open_een" d="M15.977,29.993c-3.45,0.001-6.706-1.236-9.307-3.563C3.702,23.775,2,19.971,2,15.992
                                            C2,8.75,7.647,2.621,14.857,2.039c0.422-0.038,0.849,0.111,1.161,0.398C16.329,2.724,16.5,3.114,16.5,3.536v11.171
                                            c0,0.433,0.353,0.785,0.786,0.785h4.5c0.276,0,0.5,0.224,0.5,0.5s-0.224,0.5-0.5,0.5h-4.5c-0.984,0-1.786-0.801-1.786-1.785V3.536
                                            c0-0.14-0.057-0.269-0.16-0.363c-0.108-0.101-0.249-0.148-0.402-0.138C8.244,3.576,3,9.268,3,15.992
                                            c0,3.694,1.581,7.228,4.336,9.692c2.793,2.499,6.408,3.646,10.162,3.224c5.899-0.658,10.696-5.41,11.405-11.299
                                            c0.764-6.345-3.1-12.266-9.186-14.078c-0.265-0.079-0.416-0.357-0.336-0.622c0.079-0.264,0.356-0.412,0.622-0.337
                                            c6.555,1.953,10.716,8.327,9.894,15.156c-0.764,6.345-5.932,11.465-12.287,12.174C17.062,29.963,16.517,29.993,15.977,29.993z"/>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <h3 class="text-white/80 font-medium text-lg mb-2">Próximamente</h3>
                        <p class="text-white/60 text-sm">Nueva funcionalidad en desarrollo</p>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Barra lateral con novedades y eventos -->
        <div class="space-y-6">
            
            <!-- Sección Novedades -->
            <div class="dashboard-card glass-effect rounded-3xl p-6 shadow-xl fade-in fade-in-delay-3">
                <div class="section-header flex items-center mb-6 pl-6">
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            ✨ Novedades
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">Últimas actualizaciones</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-red-50 to-yellow-50 rounded-2xl p-4 border border-red-100 
                               hover:shadow-md transition-all duration-300">
                        <div class="flex items-start space-x-3">
                            <div class="bg-red-500 rounded-full p-2 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 text-sm">Sistema actualizado</h4>
                                <p class="text-gray-600 text-xs mt-1">Nuevas funcionalidades disponibles</p>
                                <span class="text-xs text-red-600 font-medium">Hace 2 horas</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-yellow-50 to-red-50 rounded-2xl p-4 border border-yellow-100
                               hover:shadow-md transition-all duration-300">
                        <div class="flex items-start space-x-3">
                            <div class="bg-yellow-500 rounded-full p-2 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 text-sm">Mejoras de rendimiento</h4>
                                <p class="text-gray-600 text-xs mt-1">Optimizaciones implementadas</p>
                                <span class="text-xs text-yellow-600 font-medium">Ayer</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección Eventos -->
            <div class="dashboard-card glass-effect rounded-3xl p-6 shadow-xl fade-in fade-in-delay-4">
                <div class="section-header flex items-center mb-6 pl-6">
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            📅 Capacitaciones IA
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">Horario semanal fijo</p>
                    </div>
                </div>
                
                <!-- Horario semanal -->
                <div class="bg-gradient-to-r from-red-50 to-yellow-50 rounded-2xl p-4 border border-red-100 mb-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-red-500 rounded-full p-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">Horario Fijo</h4>
                            <p class="text-gray-600 text-xs">12:00 PM - 1:30 PM</p>
                        </div>
                    </div>
                    <div class="text-center bg-white/50 rounded-lg py-2 px-3">
                        <p class="text-xs font-medium text-gray-700">🤖 Sesiones de Capacitación en IA</p>
                    </div>
                </div>

                <!-- Días de la semana -->
                <div class="space-y-3">
                    <div class="bg-gradient-to-r from-red-50 to-yellow-50 rounded-xl p-3 border border-red-100
                               hover:shadow-md transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-sm">Lunes</h4>
                                    <p class="text-gray-600 text-xs">Capacitación IA</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-red-600 font-medium">12:00-13:30</p>
                                <p class="text-xs text-gray-500">Activa</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-yellow-50 to-red-50 rounded-xl p-3 border border-yellow-100
                               hover:shadow-md transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-sm">Miércoles</h4>
                                    <p class="text-gray-600 text-xs">Capacitación IA</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-yellow-600 font-medium">12:00-13:30</p>
                                <p class="text-xs text-gray-500">Activa</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-red-50 to-yellow-50 rounded-xl p-3 border border-red-100
                               hover:shadow-md transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-sm">Viernes</h4>
                                    <p class="text-gray-600 text-xs">Capacitación IA</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-red-600 font-medium">12:00-13:30</p>
                                <p class="text-xs text-gray-500">Activa</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="mt-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-3 border border-gray-200">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs text-gray-600">
                            <span class="font-medium">Próxima sesión:</span> 
                            @php
                                $today = now();
                                $daysOfWeek = ['Monday', 'Wednesday', 'Friday'];
                                $nextSession = null;
                                
                                foreach ($daysOfWeek as $day) {
                                    $nextDate = $today->next($day);
                                    if (!$nextSession || $nextDate->lt($nextSession)) {
                                        $nextSession = $nextDate;
                                    }
                                }
                            @endphp
                            {{ $nextSession ? $nextSession->format('d/m/Y') : 'Por definir' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="dashboard-card glass-effect rounded-3xl p-6 shadow-xl pulse-slow fade-in fade-in-delay-4">
                <div class="text-center">
                    <h3 class="text-lg font-bold gradient-text mb-4">📊 Resumen</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-red-600">24</p>
                            <p class="text-xs text-gray-600">Noticias</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-yellow-600">17</p>
                            <p class="text-xs text-gray-600">Recomendaciones</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection