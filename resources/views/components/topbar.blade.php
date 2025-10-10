<nav class="fixed z-30 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
  
  <!-- Estilos personalizados para el topbar -->
  <style>
    .app-dropdown {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(220, 38, 38, 0.1);
      box-shadow: 0 20px 40px rgba(220, 38, 38, 0.15);
      right: 0 !important;
      top: 65px !important;
      left: auto !important;
      transform-origin: top right;
      margin-left: auto !important;
      margin-right: 0 !important;
    }

    .app-card-topbar {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid transparent;
    }

    .app-card-topbar:hover {
      transform: translateY(-3px) scale(1.02);
      background: linear-gradient(135deg, #FEF2F2 0%, #FFFBEB 100%);
      border: 1px solid rgba(220, 38, 38, 0.2);
      box-shadow: 0 8px 25px rgba(220, 38, 38, 0.15);
    }

    .app-icon-container {
      background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
      transition: all 0.3s ease;
    }

    .app-card-topbar:hover .app-icon-container {
      background: linear-gradient(135deg, #B91C1C 0%, #F59E0B 100%);
      transform: scale(1.1) rotate(5deg);
    }

    .app-title {
      background: linear-gradient(135deg, #374151 0%, #1F2937 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-weight: 600;
    }

    .app-card-topbar:hover .app-title {
      background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .dropdown-header {
      background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
      color: white;
    }

    .coming-soon-card {
      background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
      opacity: 0.7;
    }

    .coming-soon-card:hover {
      background: linear-gradient(135deg, #D1D5DB 0%, #9CA3AF 100%);
      transform: translateY(-2px);
    }

    .fade-in-dropdown {
      animation: fadeInScale 0.3s ease-out forwards;
    }

    @keyframes fadeInScale {
      from {
        opacity: 0;
        transform: scale(0.95) translateY(-10px) translateX(10px);
      }
      to {
        opacity: 1;
        transform: scale(1) translateY(0) translateX(0);
      }
    }

    .app-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      padding: 1.5rem;
    }

    @media (max-width: 640px) {
      .app-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        padding: 1rem;
      }
    }
  </style>

  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center justify-start">
       
        <a href="{{ "/"  }}" class="flex ml-2 md:mr-24">
          <img src="{{ asset('storage/img/logo.png') }}" class="h-8 mr-3" alt="Logo" />
          <span class="hidden sm:inline self-center text-xl font-semibold whitespace-nowrap">Explorador IA</span>
        </a>

      </div>
      <div class="flex items-center">
        
        

          <!-- Perfil -->
          <div class="flex items-center ml-3">
            <div class="flex items-center space-x-3">
              <p class="hidden sm:block text-sm font-semibold">{{ auth()->user()->name }}</p>

              <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button-2" aria-expanded="false" data-dropdown-toggle="dropdown-2">
                <span class="sr-only">Open user menu</span>
                <img class="w-8 h-8 rounded-full" src="{{ isset(auth()->user()->google_image)?auth()->user()->google_image: ""  }}" alt="user photo">
              </button>
            </div>
            <!-- Dropdown menu -->
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-2">
              <div class="px-4 py-3" role="none">
                <p class="text-sm text-gray-900 dark:text-white" role="none">
                  {{ auth()->user()->name }}
                </p>
                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                  {{ auth()->user()->email }}
                </p>
              </div>
              <ul class="py-1" role="none">
                <li>
                  <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Mi Perfil</a>
                </li>
          

                <li>
                  <button data-modal-target="logout-modal" data-modal-toggle="logout-modal" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 
                                dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" type="button">
                    Cerrar sesión
                  </button>
                </li>
              </ul>
            </div>
          </div>


            <!-- Apps -->
          <button type="button" data-dropdown-toggle="apps-dropdown" class="hidden p-2 text-gray-500 rounded-lg sm:flex hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
            <span class="sr-only">View notifications</span>
            <!-- Icon -->
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
          </button>
          <!-- Dropdown menu modernizado -->
          <div class="app-dropdown z-50 hidden max-w-md my-4 overflow-hidden text-base list-none rounded-2xl shadow-2xl fade-in-dropdown absolute right-0 left-auto" id="apps-dropdown" style="right: 0; left: auto; margin-left: auto; margin-right: 0;">
            <!-- Header con gradiente -->
            <div class="dropdown-header px-6 py-4 text-center relative overflow-hidden">
              <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent"></div>
              <div class="relative z-10">
                <h3 class="text-lg font-bold text-white flex items-center justify-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                  </svg>
                  Mis Aplicaciones
                </h3>
                <p class="text-white/80 text-sm mt-1">Herramientas inteligentes</p>
              </div>
            </div>

            <!-- Grid de aplicaciones -->
            <div class="app-grid">

              <!-- Buscador Inteligente -->
              <a href="{{ route('chat.index') }}" class="app-card-topbar block p-4 text-center rounded-xl">
                <div class="app-icon-container w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.847a4.5 4.5 0 003.09 3.09L15.75 12l-2.847.813a4.5 4.5 0 00-3.09 3.091zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                  </svg>
                </div>
                <div class="app-title text-xs font-semibold leading-tight">Buscador Inteligente</div>
              </a>

              <!-- Recomendaciones -->
              <a href="{{ route('recommendations.index') }}" class="app-card-topbar block p-4 text-center rounded-xl">
                <div class="app-icon-container w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 00-3.75 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                  </svg>
                </div>
                <div class="app-title text-xs font-semibold leading-tight">Recomendaciones</div>
              </a>

              <!-- Noticias -->
              <a href="{{ route('news.index') }}" class="app-card-topbar block p-4 text-center rounded-xl">
                <div class="app-icon-container w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5M5.25 19.5a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 4.5v.75m.75 4.5v10.5M19.5 21h-15a2.25 2.25 0 01-2.25-2.25V9a2.25 2.25 0 012.25-2.25h15" />
                  </svg>
                </div>
                <div class="app-title text-xs font-semibold leading-tight">Noticias</div>
              </a>

              <!-- Estadísticas -->
              <a href="{{ route('admin.stats.dashboard') }}" class="app-card-topbar block p-4 text-center rounded-xl">
                <div class="app-icon-container w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                  </svg>
                </div>
                <div class="app-title text-xs font-semibold leading-tight">Estadísticas</div>
              </a>

              <!-- Empleados -->
              <a href="{{ route('admin.employees.index') }}" class="app-card-topbar block p-4 text-center rounded-xl">
                <div class="app-icon-container w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                  </svg>
                </div>
                <div class="app-title text-xs font-semibold leading-tight">Empleados</div>
              </a>

              <!-- Soporte Técnico -->
              <a href="{{ route('tech-support.index') }}" class="app-card-topbar block p-4 text-center rounded-xl">
                <div class="app-icon-container w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M21.75 6.75a4.5 4.5 0 01-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 11-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 016.336-4.486l-3.276 3.276a3.004 3.004 0 002.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852z" />
                  </svg>
                </div>
                <div class="app-title text-xs font-semibold leading-tight">Soporte Técnico</div>
              </a>

              <!-- Tarjetas "Próximamente" -->
              <div class="coming-soon-card app-card-topbar block p-4 text-center rounded-xl cursor-not-allowed">
                <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center bg-gray-300">
                  <svg version="1.1" id="Uploaded to svgrepo.com" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                    width="32px" height="32px" viewBox="0 0 32 32" xml:space="preserve">

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
                <div class="text-xs font-medium text-gray-500 leading-tight">Próximamente</div>
              </div>

              <div class="coming-soon-card app-card-topbar block p-4 text-center rounded-xl cursor-not-allowed">
                <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center bg-gray-300">
                  <svg version="1.1" id="Uploaded to svgrepo.com" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                    width="32px" height="32px" viewBox="0 0 32 32" xml:space="preserve">

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
                <div class="text-xs font-medium text-gray-500 leading-tight">Próximamente</div>
              </div>

              <div class="coming-soon-card app-card-topbar block p-4 text-center rounded-xl cursor-not-allowed">
                <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center bg-gray-300">
                  <svg version="1.1" id="Uploaded to svgrepo.com" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                    width="32px" height="32px" viewBox="0 0 32 32" xml:space="preserve">

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
                <div class="text-xs font-medium text-gray-500 leading-tight">Próximamente</div>
              </div>

            </div>

            <!-- Footer con acceso rápido -->
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
              <div class="flex items-center justify-center space-x-4 text-xs text-gray-600">
                <span class="flex items-center">
                  <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  Sistema activo
                </span>
                <span class="text-gray-400">•</span>
                <span>6 aplicaciones</span>
              </div>
            </div>
          </div>

          <div id="logout-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="logout-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <div class="p-4 md:p-5 text-center">
                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">¿Desea cerrar sesión?</h3>
                        
                        <div class="flex justify-center">

                            <form method="POST" action="{{ route('google.logout') }}" class="block">
                                @csrf
                                
                              <button data-modal-hide="popup-modal" type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                  Si, Cerrar Sesión
                              </button>
                            </form>
                            
                            <button data-modal-hide="logout-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">No, Cancelar</button>
                        </div>
              
                    </div>
                </div>
            </div>
          </div>
          
        </div>
    </div>
  </div>
</nav>