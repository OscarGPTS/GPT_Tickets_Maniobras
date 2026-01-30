# 🔥 Firebase Cloud Messaging - Documentación

Sistema de notificaciones push para enviar mensajes a la app móvil usando Firebase Cloud Messaging.

## 📋 Configuración

### 1. Descargar Service Account Key

1. Ve a [Firebase Console](https://console.firebase.google.com/project/maniobras-cdd65/settings/serviceaccounts/adminsdk)
2. Clic en **"Generate new private key"**
3. Guarda el archivo JSON descargado

### 2. Guardar Credenciales

```bash
# Crear directorio
mkdir storage\app\firebase

# Mover archivo descargado
move "Downloads\maniobras-cdd65-*.json" "storage\app\firebase\serviceAccountKey.json"

# Proteger el archivo
echo "storage/app/firebase/" >> .gitignore
```

### 3. Configurar .env

Agrega al final de tu `.env`:

```env
# Firebase Cloud Messaging
FIREBASE_CREDENTIALS=storage/app/firebase/serviceAccountKey.json
FIREBASE_PROJECT_ID=
FIREBASE_API_KEY=
FIREBASE_AUTH_DOMAIN=
FIREBASE_STORAGE_BUCKET=
FIREBASE_MESSAGING_SENDER_ID=
FIREBASE_APP_ID=1:
FIREBASE_MEASUREMENT_ID=
```

### 4. Limpiar Caché

```bash
php artisan config:clear
```

## 🚀 Uso

### API de Prueba (GET - Sin Autenticación)

**Endpoint:** `GET /api/test-notification/{userId}`

**Ejemplo:**
```bash
http://localhost/api/test-notification/1
```

Esta API envía una notificación de prueba al topic `user_1`.

### Enviar Notificación a un Usuario

**Endpoint:** `POST /api/fcm/send-to-user`  
**Autenticación:** Requerida

**Body:**
```json
{
  "user_id": 1,
  "title": "Nuevo Ticket",
  "body": "Se te ha asignado el ticket #123",
  "data": {
    "ticket_id": "123",
    "type": "ticket_assigned"
  },
  "image": "https://ejemplo.com/imagen.jpg"
}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Notificación enviada correctamente",
  "data": {
    "topic": "user_1",
    "user_id": 1,
    "message_id": "projects/maniobras-cdd65/messages/123456"
  }
}
```

### Enviar a Múltiples Usuarios

**Endpoint:** `POST /api/fcm/send-to-multiple`  
**Autenticación:** Requerida

**Body:**
```json
{
  "user_ids": [1, 2, 3],
  "title": "Actualización",
  "body": "Nuevo anuncio del sistema",
  "data": {
    "type": "announcement"
  }
}
```

## 💡 Uso en Código

### En tu Controlador

```php
use App\Services\FCMService;

class TicketController extends Controller
{
    protected $fcmService;
    
    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }
    
    public function store(Request $request)
    {
        // Crear ticket
        $ticket = Ticket::create([...]);
        
        // Enviar notificación
        $this->fcmService->notifyTicketCreated(
            auth()->id(),
            $ticket->id,
            $ticket->title
        );
        
        return redirect()->route('tickets.show', $ticket);
    }
}
```

### Notificación Personalizada

```php
use App\Services\FCMService;

$fcmService = app(FCMService::class);

// Enviar a un usuario
$fcmService->sendToUser(
    $userId = 1,
    $title = 'Título',
    $body = 'Mensaje',
    $data = ['key' => 'value'],
    $image = 'https://...'  // opcional
);

// Enviar a múltiples usuarios
$fcmService->sendToMultipleUsers(
    $userIds = [1, 2, 3],
    $title = 'Título',
    $body = 'Mensaje'
);
```

### Métodos Disponibles

- `sendToUser($userId, $title, $body, $data, $image)` - Enviar a un usuario
- `sendToMultipleUsers($userIds, $title, $body, $data, $image)` - Enviar a varios usuarios
- `notifyTicketCreated($userId, $ticketId, $title)` - Notificación de ticket creado
- `notifyTicketAssigned($userId, $ticketId, $title)` - Notificación de ticket asignado
- `notifyTicketStatusChanged($userId, $ticketId, $status)` - Notificación de cambio de estado

## 📱 App Móvil

### Topics

Las notificaciones se envían a topics con el formato: `user_{user_id}`

**Ejemplos:**
- Usuario ID 1 → Topic: `user_1`
- Usuario ID 25 → Topic: `user_25`

### Suscripción en la App Móvil

La app móvil debe suscribirse al topic del usuario al iniciar sesión:

```dart
// Flutter
import 'package:firebase_messaging/firebase_messaging.dart';

final userId = currentUser.id;
await FirebaseMessaging.instance.subscribeToTopic('user_$userId');
```

```javascript
// React Native
import messaging from '@react-native-firebase/messaging';

const userId = currentUser.id;
await messaging().subscribeToTopic(`user_${userId}`);
```

### Recibir Notificaciones

```dart
// Flutter
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  print('Título: ${message.notification?.title}');
  print('Cuerpo: ${message.notification?.body}');
  print('Data: ${message.data}');
  
  // Navegar según el tipo
  if (message.data['type'] == 'ticket_assigned') {
    Navigator.pushNamed(
      context,
      '/ticket',
      arguments: message.data['ticket_id'],
    );
  }
});
```

## 🔒 Seguridad

- ✅ Credenciales en `.env` (no hardcodeadas)
- ✅ Service Account Key en `.gitignore`
- ✅ Autenticación requerida en APIs de producción
- ✅ API de prueba solo para desarrollo
- ✅ Validación de datos en todos los endpoints

## 🐛 Troubleshooting

### No se reciben notificaciones

1. Verifica que la app móvil esté suscrita al topic:
   ```dart
   await FirebaseMessaging.instance.subscribeToTopic('user_1');
   ```

2. Revisa los logs de Laravel:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Prueba con la API de prueba:
   ```
   http://localhost/api/test-notification/1
   ```

### Error "FIREBASE_CREDENTIALS not found"

```bash
# Verifica que el archivo existe
dir storage\app\firebase\serviceAccountKey.json

# Si no existe, descárgalo desde Firebase Console
```

### Error "Permission denied"

```bash
# Windows
icacls storage\app\firebase\serviceAccountKey.json /grant Everyone:R

# Linux/Mac
chmod 644 storage/app/firebase/serviceAccountKey.json
```

## 📊 Estructura

**Topics:** `user_{user_id}`
- Un usuario puede tener múltiples dispositivos suscritos
- Todos reciben la misma notificación
- No necesitas almacenar tokens en la base de datos

**Data Payload:** Información adicional que puedes enviar con la notificación
```json
{
  "type": "ticket_assigned",
  "ticket_id": "123",
  "action": "open_ticket"
}
```

## 🔗 Enlaces

- [Firebase Console](https://console.firebase.google.com/project/maniobras-cdd65)
- [Firebase Cloud Messaging Docs](https://firebase.google.com/docs/cloud-messaging)
- [Kreait Firebase PHP SDK](https://github.com/kreait/firebase-php)

---

**Nota:** Recuerda eliminar o proteger la API de prueba en producción.
