# Sistema de Notificaciones - Configuración de Correo

## 📧 Configuración de Correo Electrónico

### Opción 1: Gmail (Recomendado para desarrollo)

1. **Habilitar "Contraseñas de aplicaciones" en tu cuenta de Google:**
   - Ve a: https://myaccount.google.com/security
   - En "Verificación en 2 pasos", actívala si no está activa
   - Busca "Contraseñas de aplicaciones"
   - Genera una nueva contraseña para "Correo"

2. **Actualiza tu archivo `.env`:**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=tu-email@gmail.com
   MAIL_PASSWORD=tu-contraseña-de-aplicacion-generada
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@sistematickets.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

### Opción 2: Mailtrap (Para pruebas de desarrollo)

1. **Regístrate en Mailtrap.io** (gratis para desarrollo)
2. **Copia las credenciales** de tu inbox de prueba
3. **Actualiza tu archivo `.env`:**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=tu-username-mailtrap
   MAIL_PASSWORD=tu-password-mailtrap
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@sistematickets.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

### Opción 3: Log (Para desarrollo sin correo real)

Si solo quieres probar sin enviar correos reales:

```env
MAIL_MAILER=log
```

Los correos se guardarán en `storage/logs/laravel.log`

---

## 🔔 Tipos de Notificaciones Implementadas

### 1. **Ticket Creado** (`TicketCreatedNotification`)
- **Enviado a:** Todos los usuarios con rol `almacen`
- **Cuándo:** Al crear un nuevo ticket
- **Canales:** Base de datos + Email
- **Contenido:** Información del ticket y enlace para verlo

### 2. **Ticket Asignado** (`TicketAssignedNotification`)
- **Enviado a:** Usuario que creó el ticket
- **Cuándo:** Cuando un miembro de almacén se asigna el ticket
- **Canales:** Base de datos + Email
- **Contenido:** Información del ticket y quién lo tomó

### 3. **Ticket Completado** (`TicketCompletedNotification`)
- **Enviado a:** Usuario que creó el ticket
- **Cuándo:** Cuando el miembro de almacén completa el trabajo
- **Canales:** Base de datos + Email
- **Contenido:** Información del trabajo realizado + llamado a calificar

### 4. **Encuesta Completada** (`SurveyCompletedNotification`)
- **Enviado a:** Miembro de almacén que completó el ticket
- **Cuándo:** Cuando el usuario califica el servicio
- **Canales:** Base de datos + Email
- **Contenido:** Calificación recibida y comentarios

---

## 🛠️ Configuración del Sistema de Colas

Para que las notificaciones se envíen en segundo plano (recomendado para producción):

### 1. Asegúrate de que tu `.env` tenga:
```env
QUEUE_CONNECTION=database
```

### 2. Ejecuta las migraciones de colas:
```bash
php artisan queue:table
php artisan migrate
```

### 3. Inicia el worker de colas:
```bash
php artisan queue:work
```

O para que se reinicie automáticamente:
```bash
php artisan queue:listen
```

### 4. Para producción, configura Supervisor:
Crea un archivo `/etc/supervisor/conf.d/laravel-worker.conf`:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta-a-tu-proyecto/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/ruta-a-tu-proyecto/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 🧪 Probar el Sistema de Notificaciones

### Método 1: Crear un ticket de prueba
1. Inicia sesión como usuario normal
2. Crea un nuevo ticket
3. Verifica que los usuarios de almacén reciban la notificación
4. Revisa el archivo `storage/logs/laravel.log` para ver los logs

### Método 2: Comando Artisan para pruebas
```bash
php artisan tinker
```

```php
use App\Models\User;
use App\Models\Ticket;
use App\Notifications\TicketCreatedNotification;

$almacenUser = User::role('almacen')->first();
$ticket = Ticket::first();

$almacenUser->notify(new TicketCreatedNotification($ticket));
```

### Método 3: Ver notificaciones en la base de datos
```sql
SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10;
```

---

## 📊 Estructura de Notificaciones en BD

Cada notificación se guarda con la siguiente estructura en la tabla `notifications`:

```json
{
    "ticket_id": 1,
    "ticket_title": "Problema con la impresora",
    "user_name": "Juan Pérez",
    "message": "Nuevo ticket creado: Problema con la impresora",
    "action_url": "http://localhost:8000/almacen/tickets/1",
    "type": "ticket_created",
    "icon": "fa-ticket-alt",
    "color": "blue"
}
```

---

## 🔧 Personalización de Plantillas de Correo

Las plantillas de correo están en las clases de notificación. Para personalizarlas:

1. Publica las vistas de correo de Laravel:
```bash
php artisan vendor:publish --tag=laravel-mail
```

2. Edita las plantillas en `resources/views/vendor/mail/`

---

## 🚨 Manejo de Errores

Todos los envíos de notificaciones están envueltos en bloques `try-catch`:

```php
try {
    $user->notify(new TicketCreatedNotification($ticket));
    Log::info('Notificación enviada correctamente');
} catch (\Exception $e) {
    Log::error('Error al enviar notificación: ' . $e->getMessage());
    // El proceso continúa sin interrumpirse
}
```

Los errores se registran en `storage/logs/laravel.log` pero no detienen el flujo de la aplicación.

---

## 📝 Logs de Notificaciones

Revisa los logs para ver el estado de las notificaciones:

```bash
tail -f storage/logs/laravel.log
```

Busca entradas como:
- `"Notificación enviada a X usuarios de almacén para ticket #Y"`
- `"Error al enviar notificación de ticket creado: ..."`

---

## ✅ Checklist de Implementación

- [x] Tabla de notificaciones creada
- [x] 4 tipos de notificaciones implementadas
- [x] Sistema de try-catch para manejo de errores
- [x] Logs informativos agregados
- [x] Interfaz de notificaciones en el header
- [x] Página de notificaciones (`/notifications`)
- [x] Rutas de notificaciones configuradas
- [x] Preparado para envío de correos
- [x] Sistema de colas configurado
- [ ] Configurar credenciales de correo en `.env`
- [ ] Probar envío de notificaciones
- [ ] Iniciar worker de colas en producción

---

## 🎯 Próximos Pasos

1. **Configura las credenciales de correo** en tu archivo `.env`
2. **Prueba el sistema** creando un ticket
3. **Revisa los logs** para verificar que todo funciona
4. **En producción:** Configura Supervisor para los workers de cola
5. **Opcional:** Personaliza las plantillas de correo

---

## 💡 Consejos

- **Desarrollo:** Usa `MAIL_MAILER=log` para no enviar correos reales
- **Pruebas:** Usa Mailtrap.io para ver cómo se ven los correos
- **Producción:** Usa Gmail con contraseña de aplicación o un servicio profesional como SendGrid/Mailgun
- **Performance:** Asegúrate de que el worker de colas esté corriendo en producción

---

## 🆘 Soporte

Si encuentras problemas:
1. Revisa `storage/logs/laravel.log`
2. Verifica que la tabla `notifications` existe
3. Confirma que los usuarios tienen roles asignados correctamente
4. Prueba enviar un correo manualmente con `php artisan tinker`
