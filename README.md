# Sistema de Gestión de Tickets

Sistema de tickets desarrollado con Laravel 11 para administración de solicitudes de movimiento de carga con autenticación por Auth0/Google OAuth, gestión de roles y encuestas de satisfacción.

## Requisitos

- PHP 8.2 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Composer 2.x
- Node.js 18+ y NPM
- Cuenta configurada en Auth0
- Aplicación OAuth2 configurada en Google Cloud Console

## Instalación

### 1. Clonar repositorio e instalar dependencias

```bash
git clone <repository-url> GPT_Tickets
cd GPT_Tickets
composer install
npm install
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con los valores correspondientes:

```env
APP_NAME="Sistema de Tickets"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

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

# Email (opcional para notificaciones)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tuempresa.com
MAIL_FROM_NAME="Sistema de Tickets"
```

### 3. Configurar base de datos

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 4. Compilar assets

```bash
npm run dev
# o para producción:
npm run build
```

### 5. Ejecutar servidor

```bash
php artisan serve
```

El sistema estará disponible en http://localhost:8000

## Estructura del Sistema

### Roles de Usuario

- **Admin**: Acceso completo a gestión de usuarios, visualización de tickets, estadísticas y configuración del sistema.
- **Almacén**: Puede ver, actualizar y completar tickets asignados. Visualiza estadísticas de desempeño.
- **Solicitante**: Puede crear nuevas solicitudes (tickets) y dar seguimiento a su estado.

### Flujo de Tickets

1. Solicitante crea ticket con descripción e imágenes (máximo 5)
2. Admin visualiza y asigna a miembro de almacén
3. Almacén recibe notificación y actualiza estado
4. Al finalizar, carga evidencias fotográficas
5. Solicitante completa encuesta de satisfacción
6. Admin monitorea métricas de satisfacción

### Estados de Ticket

- **Pendiente**: Esperando asignación
- **En Proceso**: Asignado y en ejecución
- **Finalizado**: Completado exitosamente
- **Cancelado**: Cancelado por motivos diversos

## Gestión de Usuarios

### Panel de Administración

Acceder a `/admin/dashboard` para:

- Ver estadísticas del sistema
- Gestionar usuarios (crear, editar, eliminar)
- Asignar roles a usuarios
- Monitorear tickets pendientes
- Visualizar métricas de satisfacción

### Alta de Usuarios

En `/admin/users/create`:

- Importar usuarios de API externa (sistema principal)
- Los datos se cargan automáticamente al abrir la página
- Seleccionar usuario y completar el formulario
- Asignar uno o más roles
- Sistema valida que no existan duplicados por email

### Integración con API Externa

El sistema carga automáticamente usuarios desde:

```
https://services.satechenergy.com/api/rh/users
```

Campos sincronizados: nombre completo, email, puesto, departamento, área.

## Notificaciones

Todas las notificaciones se almacenan en base de datos. Los eventos notificables incluyen:

- Ticket creado
- Ticket asignado
- Ticket en progreso
- Ticket completado
- Ticket cancelado
- Encuesta pendiente de completar
- Encuesta completada

Las notificaciones por email son opcionales y se configuran en `.env`.

## Seguridad

### Middleware de Autenticación

- Todas las rutas requieren autenticación con Auth0/Google
- Validación de roles mediante `Spatie\Permission`
- Protección CSRF en formularios
- Validación de entrada en todos los formularios

### Mejores Prácticas Implementadas

1. **Validación**: Todos los formularios validan entrada antes de procesamiento
2. **Autorización**: Controladores verifican permisos antes de ejecutar acciones
3. **Encriptación**: Contraseñas se encriptan con bcrypt
4. **Logs**: Todas las acciones críticas se registran en logs
5. **Paginación**: Listados usan paginación para optimizar rendimiento
6. **Modelos**: Relaciones y scopes optimizados para query efficiency

## Configuración de Auth0

1. Crear aplicación tipo "Regular Web Application" en Auth0
2. Configurar "Allowed Callback URLs":
   ```
   http://localhost:8000/auth/auth0/callback
   ```
3. Configurar "Allowed Logout URLs":
   ```
   http://localhost:8000
   ```
4. Copiar Domain, Client ID y Client Secret a `.env`

## Desarrollo

### Estructura de Carpetas

```
app/
  ├── Http/Controllers/
  │   ├── AdminController.php          # Gestión admin y usuarios
  │   ├── TicketController.php         # Lógica de tickets
  │   └── Auth/GoogleAuthController.php
  ├── Models/
  │   ├── User.php
  │   ├── Ticket.php
  │   ├── Survey.php
  │   └── TicketImage.php
  └── Notifications/                   # Notificaciones
resources/
  └── views/
      ├── admin/                       # Panel administrativo
      ├── tickets/                     # Gestión de tickets
      └── layouts/
routes/
  ├── admin.php                        # Rutas admin
  ├── auth.php                         # Rutas de autenticación
  └── web.php                          # Rutas públicas
```

### Comandos Útiles

```bash
# Ejecutar migraciones
php artisan migrate

# Crear modelo con migraciones
php artisan make:model NombreModelo -m

# Ejecutar seeders
php artisan db:seed

# Limpiar caches
php artisan optimize:clear
php artisan view:clear

# Ver rutas registradas
php artisan route:list
```

## Testing

```bash
php artisan test
```

## Producción

### Checklist Pre-Deployment

- [ ] `APP_DEBUG=false` en `.env`
- [ ] `APP_ENV=production` en `.env`
- [ ] Ejecutar `php artisan optimize`
- [ ] Ejecutar `php artisan config:cache`
- [ ] Ejecutar `php artisan route:cache`
- [ ] Ejecutar `npm run build` para assets
- [ ] Verificar permisos de carpeta `storage/` y `bootstrap/cache/`
- [ ] Configurar dominio en Auth0
- [ ] Backup de base de datos configurado

### Deploy

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan optimize
php artisan queue:restart
```

## Soporte

Para reportar issues o solicitar features, crear un issue en el repositorio con descripción detallada.

## Licencia

Propietario
