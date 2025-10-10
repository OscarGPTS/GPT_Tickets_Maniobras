# Sistema de Tickets para Movimiento de Cargas

Un sistema completo para gestionar solicitudes de movimiento de carga dentro de una organización, desarrollado con Laravel, Auth0, y Tailwind CSS.

## 🚀 Características

- **Autenticación con Auth0 + Google**: Login seguro con cuentas corporativas de Google
- **Gestión de Roles**: Usuarios normales, personal de almacén y administradores
- **Sistema de Tickets**: Creación, asignación y seguimiento de solicitudes
- **Carga de Imágenes**: Soporte para evidencias visuales en solicitudes y respuestas
- **Encuestas de Satisfacción**: Sistema obligatorio de feedback para usuarios
- **Notificaciones**: Alertas automáticas por email y en la aplicación
- **Dashboard Interactivo**: Métricas y estadísticas en tiempo real
- **Responsive Design**: Compatible con dispositivos móviles y escritorio

## 🏗️ Arquitectura del Sistema

### Roles de Usuario

1. **Usuario Solicitante**
   - Crear solicitudes de movimiento de carga
   - Subir imágenes descriptivas
   - Recibir notificaciones del progreso
   - Completar encuestas de satisfacción

2. **Personal de Almacén**
   - Ver solicitudes pendientes
   - Asignar solicitudes a sí mismos
   - Subir evidencias del trabajo realizado
   - Marcar solicitudes como completadas

3. **Administradores**
   - Acceso completo al sistema
   - Métricas y reportes avanzados
   - Gestión de usuarios y configuraciones

### Flujo de Trabajo

1. **Creación**: Usuario crea solicitud con descripción e imágenes
2. **Notificación**: El equipo de almacén recibe notificación automática
3. **Asignación**: Miembro del almacén toma la solicitud
4. **Proceso**: Se ejecuta el trabajo con evidencias fotográficas
5. **Finalización**: Se marca como completada y se notifica al usuario
6. **Encuesta**: Usuario completa encuesta de satisfacción obligatoria
7. **Nuevo Ciclo**: Usuario puede crear nueva solicitud tras completar encuesta

## 📋 Requisitos del Sistema

- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Node.js 18+ & NPM
- Cuenta de Auth0 configurada
- Aplicación de Google OAuth configurada

## 🛠️ Instalación

### 1. Clonar el Repositorio

```bash
git clone <repository-url>
cd GPT_Tickets
```

### 2. Instalar Dependencias

```bash
# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node.js
npm install
```

### 3. Configurar Variables de Entorno

```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar Base de Datos

Crear base de datos MySQL y actualizar las credenciales en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gpt_tickets
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 5. Configurar Auth0

1. Crear cuenta en [Auth0](https://auth0.com)
2. Crear una nueva aplicación web
3. Configurar Google como proveedor social
4. Actualizar variables en `.env`:

```env
AUTH0_DOMAIN=tu-dominio.auth0.com
AUTH0_CLIENT_ID=tu-client-id
AUTH0_CLIENT_SECRET=tu-client-secret
AUTH0_REDIRECT_URI=http://localhost:8000/auth/auth0/callback
```

### 6. Configurar Google OAuth

1. Ir a [Google Cloud Console](https://console.cloud.google.com)
2. Crear proyecto y habilitar Google+ API
3. Crear credenciales OAuth 2.0
4. Configurar en Auth0 como proveedor social

### 7. Ejecutar Migraciones

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (opcional)
php artisan db:seed
```

### 8. Configurar Storage

```bash
# Crear enlace simbólico para archivos públicos
php artisan storage:link
```

### 9. Compilar Assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 10. Iniciar Servidor

```bash
# Servidor de desarrollo
php artisan serve

# La aplicación estará disponible en: http://localhost:8000
```

## ⚙️ Configuración Adicional

### Correo Electrónico

Para habilitar notificaciones por email, configurar SMTP en `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="Sistema de Tickets"
```

### Colas de Trabajo (Opcional)

Para procesar notificaciones en segundo plano:

```bash
# Configurar driver de cola
QUEUE_CONNECTION=database

# Crear tabla de trabajos
php artisan queue:table
php artisan migrate

# Ejecutar worker
php artisan queue:work
```

### Configuración de Roles

Por defecto, todos los usuarios nuevos tienen rol "user". Para asignar roles de almacén:

1. Acceder a la base de datos
2. Actualizar campo `role` en tabla `users`:
   - `'user'` - Usuario normal
   - `'almacen'` - Personal de almacén
   - `'admin'` - Administrador

```sql
UPDATE users SET role = 'almacen' WHERE email = 'empleado@empresa.com';
UPDATE users SET role = 'admin' WHERE email = 'admin@empresa.com';
```

## 🗄️ Estructura de Base de Datos

### Tablas Principales

- **users**: Información de usuarios y roles
- **tickets**: Solicitudes de movimiento de carga
- **ticket_images**: Imágenes asociadas a tickets
- **surveys**: Encuestas de satisfacción
- **notifications**: Notificaciones del sistema

### Relaciones

- Usuario → Muchos Tickets
- Ticket → Muchas Imágenes
- Ticket → Una Encuesta
- Usuario → Muchas Notificaciones

## 🔧 Comandos Artisan Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Verificar configuración
php artisan config:show

# Ver rutas
php artisan route:list

# Ejecutar migraciones frescas
php artisan migrate:fresh --seed
```

## 📱 Uso del Sistema

### Para Usuarios

1. **Acceder**: Ir a `/login` y autenticarse con Google
2. **Crear Ticket**: Usar botón "Nueva Solicitud" en dashboard
3. **Seguimiento**: Ver progreso en "Mis Tickets"
4. **Encuestas**: Completar encuestas pendientes para crear nuevos tickets

### Para Personal de Almacén

1. **Panel**: Acceder a "Panel Almacén" en navegación
2. **Tickets Pendientes**: Ver todas las solicitudes sin asignar
3. **Tomar Ticket**: Asignarse solicitudes para trabajar
4. **Completar**: Subir evidencias y marcar como finalizado

### Para Administradores

1. **Dashboard Admin**: Métricas completas del sistema
2. **Reportes**: Estadísticas de satisfacción y eficiencia
3. **Gestión**: Control total sobre usuarios y configuraciones

## 🛡️ Seguridad

- **Autenticación OAuth**: Solo usuarios con cuentas Google autorizadas
- **Autorización por Roles**: Acceso controlado por funciones
- **Validación de Archivos**: Verificación de tipo y tamaño de imágenes
- **Protección CSRF**: Protección contra ataques de falsificación
- **Sanitización**: Limpieza de entradas de usuario

## 📊 Métricas y Reportes

El sistema incluye dashboards con:

- **Tickets por Estado**: Pendientes, en proceso, completados
- **Tiempo de Respuesta**: Métricas de eficiencia del almacén
- **Satisfacción del Cliente**: Promedios de calificaciones
- **Productividad**: Tickets completados por empleado
- **Tendencias**: Gráficos de actividad temporal

## 🚀 Despliegue en Producción

### Lista de Verificación

- [ ] Configurar base de datos de producción
- [ ] Actualizar variables de entorno
- [ ] Configurar servidor web (Apache/Nginx)
- [ ] Habilitar HTTPS
- [ ] Configurar cron jobs para colas
- [ ] Establecer backups automáticos
- [ ] Configurar monitoreo de logs

### Variables de Entorno Críticas

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com
AUTH0_DOMAIN=tu-dominio-prod.auth0.com
MAIL_MAILER=smtp
QUEUE_CONNECTION=redis
```

## 🤝 Contribuir

1. Fork del repositorio
2. Crear rama para nueva característica
3. Realizar cambios y pruebas
4. Enviar pull request

## 📝 Licencia

Este proyecto está licenciado bajo la Licencia MIT.

## 🆘 Soporte

Para soporte técnico:

1. Revisar documentación
2. Verificar logs en `storage/logs/laravel.log`
3. Consultar issues en GitHub
4. Contactar al equipo de desarrollo

---

**Sistema de Tickets v1.0** - Desarrollado con ❤️ para optimizar la gestión de almacenes
