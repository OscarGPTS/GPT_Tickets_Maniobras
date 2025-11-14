# Configuración de Notificaciones

Sistema de notificaciones del sistema de tickets con soporte para base de datos y correo electrónico.

## 📧 Tipos de Notificaciones

El sistema envía 6 tipos de notificaciones diferentes:

| Evento | Archivo | Destinatario | Descripción |
|--------|---------|--------------|-------------|
| **Ticket Creado** | `TicketCreated.php` | Personal de almacén | Cuando se crea un nuevo ticket |
| **Ticket Asignado** | `TicketAssigned.php` | Miembro asignado | Cuando un ticket es asignado |
| **Ticket en Progreso** | `TicketProgress.php` | Solicitante | Cuando cambia a estado "en_progreso" |
| **Ticket Completado** | `TicketCompleted.php` | Solicitante | Cuando el trabajo es finalizado |
| **Ticket Cancelado** | `TicketCancelledNotification.php` | Almacén asignado | Cuando el solicitante cancela |
| **Encuesta Completada** | `SurveyCompletedNotification.php` | Almacén asignado | Cuando se califica el servicio |

## 🔧 Estado Actual

**Por defecto, las notificaciones por email están DESACTIVADAS**. Solo se guardan en la base de datos.

Esto permite:
- ✅ Probar el sistema sin configurar servidor de correo
- ✅ Ver notificaciones en la interfaz web
- ✅ Activar emails cuando sea necesario

## 📤 Activar Notificaciones por Email

Para activar emails en cualquier evento, modificar el archivo correspondiente en `app/Notifications/`:

### Ejemplo: Activar emails para Ticket Creado

**Archivo:** `app/Notifications/TicketCreated.php`

```php
public function via(object $notifiable): array
{
    // ANTES (solo base de datos):
    return ['database'];
    
    // DESPUÉS (base de datos + email):
    return ['mail', 'database'];
}
```

### Activar Todos los Emails

Cambiar `return ['database'];` por `return ['mail', 'database'];` en estos archivos:

1. `app/Notifications/TicketCreated.php` (línea ~29)
2. `app/Notifications/TicketAssigned.php` (línea ~29)
3. `app/Notifications/TicketProgress.php` (línea ~32)
4. `app/Notifications/TicketCompleted.php` (línea ~27)
5. `app/Notifications/TicketCancelledNotification.php` (línea ~29)
6. `app/Notifications/SurveyCompletedNotification.php` (línea ~29)

## 📨 Plantillas de Email

Las plantillas HTML están en `resources/views/emails/tickets/`:

```
emails/tickets/
├── created.blade.php           # Nuevo ticket
├── assigned.blade.php          # Ticket asignado
├── in-progress.blade.php       # En progreso
├── completed.blade.php         # Completado (con link a encuesta)
├── cancelled.blade.php         # Cancelado (muestra razón)
└── survey-completed.blade.php  # Calificación recibida (muestra estrellas)
```

### Características de las Plantillas

- ✅ Diseño responsive (móvil y escritorio)
- ✅ Header corporativo con logo GPT Services
- ✅ Footer con información de la empresa
- ✅ Colores corporativos (#CF0A2C rojo, #F9BE00 amarillo)
- ✅ Botones call-to-action para ver tickets
- ✅ Información detallada del ticket
- ✅ Estados visuales con badges de colores

## ⚙️ Configuración de Email

### Opción 1: Gmail (Desarrollo)

Configurar en `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
MAIL_FROM_NAME="GPT Services - Tickets"
```

**Importante:** Para Gmail, crear "Contraseña de aplicación":
1. Ir a cuenta de Google → Seguridad
2. Activar verificación en 2 pasos
3. Generar contraseña de aplicación
4. Usar esa contraseña en `MAIL_PASSWORD`

### Opción 2: Servidor SMTP Corporativo

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.tuempresa.com
MAIL_PORT=587
MAIL_USERNAME=tickets@tuempresa.com
MAIL_PASSWORD=contraseña_segura
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tuempresa.com
MAIL_FROM_NAME="Sistema de Tickets"
```

### Opción 3: Servicios de Email (Producción)

Para producción, usar servicios profesionales:

**SendGrid:**
```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=tu_api_key
```

**Mailgun:**
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=tu_dominio
MAILGUN_SECRET=tu_secret
```

## 🚀 Configurar Colas de Trabajo

Las notificaciones implementan `ShouldQueue` para procesarse en segundo plano.

### 1. Configurar Queue Driver

En `.env`:

```env
QUEUE_CONNECTION=database
```

### 2. Crear Tabla de Trabajos

```bash
php artisan queue:table
php artisan migrate
```

### 3. Ejecutar Worker

**Desarrollo:**
```bash
php artisan queue:work
```

**Producción (con supervisor):**

Crear archivo `/etc/supervisor/conf.d/gpt-tickets-worker.conf`:

```ini
[program:gpt-tickets-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/a/GPT_Tickets/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/ruta/a/GPT_Tickets/storage/logs/worker.log
```

Luego:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start gpt-tickets-worker:*
```

## 🧪 Probar Notificaciones

### Probar Email

```bash
php artisan tinker
```

```php
// Obtener un usuario
$user = App\Models\User::first();

// Obtener un ticket
$ticket = App\Models\Ticket::first();

// Enviar notificación de prueba
$user->notify(new App\Notifications\TicketCreated($ticket));
```

Verificar:
1. Revisar bandeja de entrada del usuario
2. Verificar tabla `notifications` en la base de datos
3. Revisar `storage/logs/laravel.log` para errores

## 📊 Monitoreo

### Ver Notificaciones en Base de Datos

```sql
SELECT * FROM notifications 
ORDER BY created_at DESC 
LIMIT 10;
```

### Ver Jobs en Cola

```sql
SELECT * FROM jobs;
SELECT * FROM failed_jobs;
```

### Reintentar Jobs Fallidos

```bash
# Ver jobs fallidos
php artisan queue:failed

# Reintentar todos
php artisan queue:retry all

# Reintentar uno específico
php artisan queue:retry [job-id]
```

## 🎨 Personalizar Plantillas

### Modificar Colores

Editar archivos en `resources/views/emails/tickets/*.blade.php`:

```css
.header {
    background: #TU_COLOR;  /* Color del header */
}

.footer-accent {
    color: #TU_COLOR;  /* Color del acento */
}

.badge {
    background: #TU_COLOR;  /* Color de badges */
}
```

### Modificar Logo

Reemplazar el SVG en la sección `<div class="logo">` de cada plantilla.

### Agregar Información

Agregar secciones HTML en el área `<div class="content">`:

```html
<div class="info-box">
    <h3>Título de Sección</h3>
    <div class="info-row">
        <span class="info-label">Etiqueta:</span>
        {{ $variable }}
    </div>
</div>
```

## ❌ Desactivar Notificaciones Específicas

Para desactivar solo cierto tipo de notificación, comentar la línea de envío en el controlador:

**Ejemplo:** Desactivar notificación de creación de ticket

En `app/Http/Controllers/Solicitante/TicketController.php`:

```php
// Comentar estas líneas:
// try {
//     $almacenUsers = User::role('almacen')->get();
//     if ($almacenUsers->count() > 0) {
//         Notification::send($almacenUsers, new TicketCreatedNotification($ticket));
//     }
// } catch (\Exception $e) {
//     Log::error('Error al enviar notificaciones: ' . $e->getMessage());
// }
```

## 🔍 Solución de Problemas

### Emails no se envían

1. **Verificar configuración SMTP**
   ```bash
   php artisan tinker
   config('mail');
   ```

2. **Revisar logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Probar conexión**
   ```bash
   php artisan tinker
   Mail::raw('Test', function($msg) { 
       $msg->to('test@example.com')->subject('Test'); 
   });
   ```

### Notificaciones duplicadas

- Verificar que no haya múltiples workers corriendo
- Revisar que `via()` no retorne canales duplicados

### Queue no procesa

- Verificar que el worker esté corriendo: `ps aux | grep queue:work`
- Limpiar trabajos viejos: `php artisan queue:flush`
- Reiniciar worker: `php artisan queue:restart`

---

**Sistema de Notificaciones - GPT Services**
