# Sistema de Tickets para Movimiento de Cargas

Sistema completo para gestionar solicitudes de movimiento de carga, desarrollado con Laravel 11, Auth0, Google OAuth y Tailwind CSS.

## 🚀 Características Principales

### Autenticación y Roles
- **Auth0 + Google OAuth**: Login seguro con cuentas corporativas
- **3 Roles de Usuario**:
  - **Solicitante**: Crea y da seguimiento a tickets
  - **Almacén**: Asigna, procesa y completa solicitudes
  - **Admin**: Acceso completo y métricas del sistema

### Gestión de Tickets
- Creación de solicitudes con descripción e imágenes (máx. 5)
- Sistema de estados: Pendiente → En Proceso → Completado/Cancelado
- Asignación automática y manual de tickets
- Carga de evidencias fotográficas del trabajo realizado
- Cancelación de tickets con razón opcional

### Notificaciones
- **Base de datos**: Todas las notificaciones se guardan
- **Email**: Configurables por evento (desactivadas por defecto)
- Notificaciones en tiempo real en la interfaz
- Badge de contador de notificaciones no leídas

### Encuestas de Satisfacción
- Sistema obligatorio de calificación (1-5 estrellas)
- Comentarios opcionales
- Bloqueo de nuevos tickets hasta completar encuestas pendientes
- Dashboard con métricas de satisfacción

## 📋 Requisitos del Sistema

- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.3+
- Composer 2.x
- Node.js 18+ & NPM
- Cuenta de Auth0
- Aplicación de Google OAuth

## 🛠️ Instalación

### 1. Clonar e Instalar Dependencias

```bash
git clone <repository-url>
cd GPT_Tickets
composer install
npm install
```

### 2. Configurar Variables de Entorno

```bash
cp .env.example .env
php artisan key:generate
```

Configurar en `.env`:

```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gpt_tickets
DB_USERNAME=root
DB_PASSWORD=

# Auth0
AUTH0_DOMAIN=tu-dominio.auth0.com
AUTH0_CLIENT_ID=tu-client-id
AUTH0_CLIENT_SECRET=tu-client-secret
AUTH0_REDIRECT_URI=http://localhost:8000/auth/auth0/callback

# Email (opcional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_password_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tuempresa.com
MAIL_FROM_NAME="Sistema de Tickets"
```

### 3. Configurar Base de Datos

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 4. Compilar Assets y Ejecutar

```bash
npm run dev  # Desarrollo
# o
npm run build  # Producción

php artisan serve
```

La aplicación estará disponible en: `http://localhost:8000`

## 🏗️ Arquitectura del Sistema

### Estructura de Carpetas por Rol

```
app/Http/Controllers/
├── Solicitante/         # Controladores para usuarios solicitantes
│   ├── DashboardController.php
│   ├── TicketController.php
│   └── SurveyController.php
├── Almacen/             # Controladores para personal de almacén
│   ├── DashboardController.php
│   └── TicketController.php
└── Admin/               # Controladores para administradores
    └── DashboardController.php

resources/views/
├── solicitante/         # Vistas para usuarios solicitantes
├── almacen/             # Vistas para personal de almacén
├── admin/               # Vistas para administradores
└── emails/tickets/      # Plantillas de correo

routes/
├── web.php              # Rutas públicas
├── solicitante.php      # Rutas de solicitantes
├── almacen.php          # Rutas de almacén
└── admin.php            # Rutas de administradores
```

### Flujo de Trabajo del Ticket

```
1. CREACIÓN
   ├─ Usuario crea ticket con título, descripción e imágenes
   └─ Notificación a todo el equipo de almacén

2. ASIGNACIÓN
   ├─ Miembro de almacén se asigna el ticket
   ├─ Estado cambia a "en_proceso"
   └─ Notificación al solicitante

3. PROCESO
   ├─ Personal de almacén trabaja en la solicitud
   └─ Puede subir evidencias fotográficas

4. FINALIZACIÓN
   ├─ Se marca como completado
   ├─ Se agregan evidencias del trabajo
   └─ Notificación al solicitante

5. CALIFICACIÓN
   ├─ Solicitante completa encuesta obligatoria
   ├─ Califica de 1-5 estrellas y agrega comentarios
   └─ Notificación al miembro de almacén

6. NUEVO CICLO
   └─ Usuario puede crear nuevos tickets
```

## 🔐 Gestión de Roles

### Asignar Roles Manualmente

Por defecto, todos los usuarios nuevos tienen rol "solicitante". Para asignar roles:

**Opción 1: Base de datos**
```sql
UPDATE users SET role = 'almacen' WHERE email = 'empleado@empresa.com';
UPDATE users SET role = 'admin' WHERE email = 'admin@empresa.com';
```

**Opción 2: Spatie Laravel Permission**
```bash
php artisan tinker
```
```php
$user = User::where('email', 'empleado@empresa.com')->first();
$user->assignRole('almacen');
```

### Permisos por Rol

| Funcionalidad | Solicitante | Almacén | Admin |
|--------------|-------------|---------|-------|
| Crear tickets | ✅ | ✅ | ✅ |
| Ver propios tickets | ✅ | ✅ | ✅ |
| Ver todos los tickets | ❌ | ✅ | ✅ |
| Asignar tickets | ❌ | ✅ | ✅ |
| Completar tickets | ❌ | ✅ | ✅ |
| Cancelar propios tickets | ✅ | ❌ | ✅ |
| Calificar tickets | ✅ | ❌ | ✅ |
| Ver métricas | ❌ | Limitadas | Completas |

## 📊 Características Técnicas

### Base de Datos

**Tablas principales:**
- `users` - Usuarios del sistema
- `tickets` - Solicitudes de movimiento
- `ticket_images` - Imágenes de tickets (solicitudes y evidencias)
- `surveys` - Encuestas de satisfacción
- `notifications` - Notificaciones del sistema

### Validaciones

- **Imágenes**: JPG, PNG, JPEG (máx. 2MB c/u, límite 5 por ticket)
- **Razón de cancelación**: Opcional, máx. 500 caracteres
- **Encuestas**: Calificación 1-5 obligatoria, comentarios opcionales
- **Edición de tickets**: Solo en estado "pendiente"

### Seguridad

- Autenticación OAuth con Auth0
- Middleware de autorización por roles
- Protección CSRF en formularios
- Validación de tipos y tamaños de archivos
- Sanitización de entradas de usuario

## 🚀 Despliegue en Producción

### Checklist

- [ ] Configurar base de datos de producción
- [ ] Actualizar variables de entorno (APP_ENV=production, APP_DEBUG=false)
- [ ] Configurar servidor web (Apache/Nginx)
- [ ] Habilitar HTTPS
- [ ] Configurar cron para colas: `* * * * * php artisan schedule:run`
- [ ] Configurar backups automáticos
- [ ] Establecer monitoreo de logs

### Comandos de Producción

```bash
# Optimizar aplicación
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build

# Configurar permisos
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🔧 Mantenimiento

### Limpiar Caché

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Ver Logs

```bash
tail -f storage/logs/laravel.log
```

### Comandos Útiles

```bash
# Ver rutas del sistema
php artisan route:list

# Ejecutar worker de colas
php artisan queue:work

# Refrescar migraciones (¡CUIDADO! Borra datos)
php artisan migrate:fresh --seed
```

## 📖 Documentación Adicional

- [Configuración de Notificaciones por Email](./NOTIFICACIONES.md)
- [Arquitectura y Mejoras del Sistema](./ARQUITECTURA.md)

## 🆘 Soporte y Solución de Problemas

### Problemas Comunes

**Error: "RouteNotFoundException"**
- Verificar que las rutas estén registradas en `routes/web.php` y archivos de rol
- Limpiar caché: `php artisan route:clear`

**Imágenes no se muestran**
- Ejecutar: `php artisan storage:link`
- Verificar permisos de `storage/app/public`

**Notificaciones no llegan por email**
- Verificar configuración SMTP en `.env`
- Activar emails en archivos de notificación (ver docs/NOTIFICACIONES.md)
- Verificar logs: `storage/logs/laravel.log`

---

**Sistema de Tickets v1.0** - GPT Services
