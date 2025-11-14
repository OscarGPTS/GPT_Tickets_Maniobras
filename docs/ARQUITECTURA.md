# Arquitectura y Mejoras del Sistema

Documentación técnica sobre la arquitectura del sistema y las mejoras implementadas.

## 🏗️ Separación por Roles (Namespace Pattern)

### Estructura de Carpetas

El sistema implementa una arquitectura limpia separando los controladores, rutas y vistas por rol:

```
app/Http/Controllers/
├── Solicitante/
│   ├── DashboardController.php    # Dashboard del solicitante
│   ├── TicketController.php       # CRUD de tickets
│   └── SurveyController.php       # Gestión de encuestas
├── Almacen/
│   ├── DashboardController.php    # Dashboard de almacén
│   └── TicketController.php       # Asignación y completado
└── Admin/
    └── DashboardController.php    # Panel de administración

routes/
├── web.php           # Rutas públicas y auth
├── solicitante.php   # Rutas: /solicitante/*
├── almacen.php       # Rutas: /almacen/*
└── admin.php         # Rutas: /admin/*

resources/views/
├── solicitante/      # Vistas de solicitantes
│   ├── dashboard.blade.php
│   └── tickets/
├── almacen/          # Vistas de almacén
│   ├── dashboard.blade.php
│   └── tickets/
└── admin/            # Vistas de administración
    └── dashboard.blade.php
```

### Beneficios de esta Arquitectura

✅ **Separación de Responsabilidades**: Cada rol tiene su propio espacio
✅ **Mantenibilidad**: Fácil ubicar y modificar código específico de un rol
✅ **Escalabilidad**: Agregar nuevos roles es sencillo
✅ **Seguridad**: Middleware específico por grupo de rutas
✅ **Mejores Prácticas**: Sigue convenciones de Laravel

### Middleware por Rol

Cada archivo de rutas aplica middleware específico:

**routes/solicitante.php:**
```php
Route::middleware(['auth', 'role:solicitante'])->prefix('solicitante')->group(function () {
    // Rutas solo para solicitantes
});
```

**routes/almacen.php:**
```php
Route::middleware(['auth', 'role:almacen'])->prefix('almacen')->group(function () {
    // Rutas solo para almacén
});
```

**routes/admin.php:**
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Rutas solo para administradores
});
```

## 📊 Dashboard Consolidado

### Antes: Tickets Separados
```php
// Dashboard mostraba tickets en tarjetas separadas
$activeTickets = $user->tickets()->whereIn('status', ['pendiente', 'en_proceso']);
$completedTickets = $user->tickets()->where('status', 'completado');
```

### Después: Tabla Unificada
```php
// Todos los tickets en una tabla paginada
$tickets = $user->tickets()
    ->with(['assignedTo', 'images', 'survey'])
    ->latest()  // Más recientes primero
    ->paginate(20);
```

### Características de la Tabla

- ✅ **Estados visuales**: Badges de colores por estado
- ✅ **Paginación**: 20 tickets por página
- ✅ **Información completa**: Estado, asignado, fecha, encuesta, acciones
- ✅ **Ordenamiento**: Más recientes primero
- ✅ **Búsqueda**: Por título y descripción (filtros en index)
- ✅ **Responsive**: Se adapta a móviles

### Estados con Colores

```blade
@if($ticket->status === 'pendiente')
    <span class="badge badge-blue">Pendiente</span>
@elseif($ticket->status === 'en_proceso')
    <span class="badge badge-yellow">En Proceso</span>
@elseif($ticket->status === 'completado')
    <span class="badge badge-green">Completado</span>
@else
    <span class="badge badge-red">Cancelado</span>
@endif
```

## 🚫 Funcionalidad de Cancelación

### Ubicación de Cancelación

**ANTES:** Modales en múltiples vistas (dashboard, index)
**AHORA:** Solo en vista de detalle del ticket

### Razón de Cancelación

- ✅ Campo **opcional** (nullable)
- ✅ Valor por defecto: "Cancelado" si se deja vacío
- ✅ Máximo 500 caracteres
- ✅ Textarea para comentarios extendidos

### Validación

```php
$request->validate([
    'cancellation_reason' => 'nullable|string|max:500',
]);

$cancellationReason = $request->cancellation_reason ?: 'Cancelado';
$ticket->cancel($cancellationReason);
```

### Flujo de Cancelación

1. Usuario ve ticket completo
2. Click en botón "Cancelar Ticket" (solo si status=pendiente)
3. Se despliega formulario con textarea opcional
4. Confirmación con JavaScript
5. Se guarda razón y se actualiza estado
6. Notificación al almacén si estaba asignado

## 🗑️ Eliminación de Tickets Deshabilitada

Para evitar manipulación de información:

- ❌ **Botón "Eliminar" removido** de vista de edición
- ✅ Solo pueden **cancelarse** tickets pendientes
- ✅ **Historial completo** mantenido en base de datos
- ✅ Tickets cancelados/completados se conservan para auditoría

### Razones

1. **Trazabilidad**: Mantener registro completo de actividades
2. **Auditoría**: Poder revisar tickets pasados
3. **Métricas**: Datos históricos para reportes
4. **Seguridad**: Evitar borrado accidental o malicioso

## 🎨 Mejoras de UI/UX

### Vista de Tickets (Index)

**Características:**
- Tabla con hover effects
- Badges visuales de estado
- Iconos Font Awesome
- Paginación con números
- Empty state cuando no hay tickets
- Botón flotante "Nueva Solicitud"

### Vista de Detalle (Show)

**Secciones:**
1. **Header**: Título, estado, fechas, botones de acción
2. **Información del Ticket**: Detalles completos
3. **Descripción**: Texto formateado
4. **Imágenes**: Galería con lightbox
5. **Asignación**: Info del responsable (si aplica)
6. **Evidencias**: Fotos del trabajo (si completado)
7. **Encuesta**: Formulario o resultados
8. **Cancelación**: Formulario oculto (toggle)

### Dashboard Cards

```html
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📋</div>
        <div class="stat-number">{{ $totalTickets }}</div>
        <div class="stat-label">Total Tickets</div>
    </div>
    <!-- Más cards... -->
</div>
```

## 🔒 Seguridad y Validaciones

### Validación de Archivos

```php
'images' => 'nullable|array|max:5',
'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
```

- Máximo 5 imágenes
- Solo JPG, PNG, GIF
- Máximo 2MB por imagen
- Validación de tipo MIME

### Autorización

```php
// Verificar propiedad del ticket
if ($ticket->user_id !== Auth::id()) {
    abort(403, 'No autorizado');
}

// Verificar rol
if (!Auth::user()->hasRole('almacen')) {
    abort(403);
}

// Verificar estado para edición
if ($ticket->status !== 'pendiente') {
    return back()->with('error', 'Solo puedes editar tickets pendientes');
}
```

### Middleware de Rutas

```php
Route::middleware(['auth', 'role:solicitante'])->group(function () {
    // Solo usuarios autenticados con rol solicitante
});
```

## 📈 Optimizaciones de Performance

### Eager Loading

```php
// Evita N+1 queries
$tickets = Ticket::with([
    'user',           // Solicitante
    'assignedTo',     // Responsable
    'images',         // Imágenes
    'survey'          // Encuesta
])->paginate(20);
```

### Paginación

```php
// 20 items por página
$tickets->paginate(20)->appends($request->except('page'));
```

### Índices de Base de Datos

```php
$table->index('status');
$table->index('user_id');
$table->index('assigned_to');
$table->index('created_at');
```

## 🔄 Patrones de Diseño Implementados

### Repository Pattern (Implícito)

Los controladores interactúan con modelos Eloquent que abstraen el acceso a datos.

### Service Pattern

```php
// app/Services/FileUploadService.php
class FileUploadService {
    public function uploadTicketImages($images, $ticketId) {
        // Lógica de subida centralizada
    }
}
```

### Observer Pattern

```php
// app/Observers/TicketObserver.php
class TicketObserver {
    public function created(Ticket $ticket) {
        // Notificar automáticamente
    }
}
```

### Middleware Pattern

Capas de procesamiento antes de llegar al controlador:
1. Autenticación
2. Verificación de rol
3. Validación CSRF
4. Sanitización de inputs

## 🧪 Testing (Recomendaciones)

### Tests Unitarios

```php
public function test_usuario_puede_crear_ticket()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post('/solicitante/tickets', [
        'title' => 'Test Ticket',
        'description' => 'Descripción de prueba'
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('tickets', ['title' => 'Test Ticket']);
}
```

### Tests de Integración

```php
public function test_flujo_completo_ticket()
{
    // 1. Crear ticket
    // 2. Asignar a almacén
    // 3. Completar
    // 4. Calificar
    // 5. Verificar estado final
}
```

## 📦 Dependencias Principales

```json
{
    "laravel/framework": "^11.0",
    "auth0/auth0-php": "^8.0",
    "spatie/laravel-permission": "^6.0",
    "intervention/image": "^3.0",
    "livewire/livewire": "^3.0" // Opcional
}
```

## 🔮 Mejoras Futuras Sugeridas

1. **API REST**: Para integración con apps móviles
2. **WebSockets**: Notificaciones en tiempo real
3. **Reportes PDF**: Generación de reportes exportables
4. **Filtros Avanzados**: Por fechas, responsables, etc.
5. **Comentarios**: Sistema de chat en tickets
6. **Historial de Cambios**: Log de todas las modificaciones
7. **Etiquetas**: Sistema de tags para categorizar
8. **Prioridades**: Niveles de urgencia

---

**Arquitectura del Sistema - GPT Services**
