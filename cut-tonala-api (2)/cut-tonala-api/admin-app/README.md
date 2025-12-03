# Interfaz Administrativa Laravel - CUT Tonalá

Interfaz administrativa desarrollada en Laravel (estructura simplificada) para consumir la API REST de Node.js.

## 📋 Requisitos

- PHP 8.1 o superior
- Composer
- Servidor web (Apache/Nginx) o PHP Built-in Server
- API REST de Node.js corriendo en `http://localhost:3000`

## 🚀 Instalación

1. **Instalar dependencias de Composer:**
```bash
cd admin-app
composer install
```

2. **Configurar variables de entorno:**
```bash
cp .env.example .env
```

Edita el archivo `.env` y configura:
```
API_BASE_URL=http://localhost:3000
API_TIMEOUT=30
```

3. **Asegúrate de que la API REST esté corriendo:**
```bash
# En el directorio raíz del proyecto
npm start
```

## 🏃 Ejecutar la aplicación

### Opción 1: Servidor PHP Built-in
```bash
cd admin-app/public
php -S localhost:8000
```

### Opción 2: Apache/Nginx
Configura tu servidor web para apuntar al directorio `admin-app/public`

## 📁 Estructura del Proyecto

```
admin-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      # Controlador de autenticación
│   │   │   └── TallerController.php    # Controlador de talleres
│   │   └── Middleware/
│   │       └── AuthMiddleware.php     # Middleware de autenticación
│   ├── Services/
│   │   └── ApiService.php             # Servicio para consumir API REST
│   └── Helpers/
│       ├── functions.php              # Funciones helper
│       └── BladeHelper.php           # Compilador de plantillas Blade
├── config/
│   ├── app.php                        # Configuración de la app
│   └── api.php                        # Configuración de la API
├── public/
│   ├── index.php                      # Punto de entrada
│   └── .htaccess                      # Configuración Apache
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php          # Layout principal
│       ├── auth/
│       │   └── login.blade.php        # Vista de login
│       └── talleres/
│           ├── index.blade.php       # Lista de talleres
│           └── show.blade.php         # Detalles de taller
└── routes/
    └── web.php                        # Rutas de la aplicación
```

## 🔐 Autenticación

La aplicación consume el endpoint de autenticación de la API REST:

- **Endpoint:** `POST /api/login`
- **Body:** `{ "correo": "usuario@ejemplo.com", "contrasena": "password123" }`
- **Respuesta:** `{ "token": "jwt_token", "usuario": {...} }`

El token JWT se almacena en la sesión y se envía automáticamente en todas las peticiones a la API mediante el header `Authorization: Bearer {token}`.

## 🛡️ Protección de Rutas

Las rutas protegidas utilizan el middleware `AuthMiddleware` que verifica la existencia del token en la sesión. Si no hay token, redirige al login.

## 📝 Rutas Disponibles

- `GET /login` - Formulario de login
- `POST /login` - Procesar login
- `POST /logout` - Cerrar sesión
- `GET /talleres` - Lista de talleres (requiere autenticación)
- `GET /talleres/{id}` - Detalles de un taller (requiere autenticación)

## 🎨 Características

- ✅ Autenticación con JWT
- ✅ Protección de rutas con middleware
- ✅ Consumo de API REST
- ✅ Manejo de errores
- ✅ Interfaz moderna con Bootstrap 5
- ✅ Responsive design
- ✅ Visualización de imágenes de talleres

## 🧪 Pruebas

1. **Iniciar sesión:**
   - Accede a `http://localhost:8000/login`
   - Ingresa las credenciales de un usuario administrador

2. **Ver talleres:**
   - Después del login, serás redirigido a la lista de talleres
   - Puedes ver todos los talleres con sus imágenes

3. **Ver detalles:**
   - Haz clic en "Ver Detalles" de cualquier taller
   - Se mostrará la información completa del taller

4. **Probar protección:**
   - Cierra sesión
   - Intenta acceder directamente a `/talleres`
   - Serás redirigido al login

## 📚 Documentación de la API

Consulta la documentación de la API REST en el archivo `GUIA_AUTENTICACION.md` del proyecto principal.

## ⚠️ Notas

- Esta es una implementación simplificada de Laravel para fines educativos
- En producción, se recomienda usar Laravel completo con todas sus características
- El sistema de plantillas Blade está simplificado y no incluye todas las características del Blade original

## 🐛 Solución de Problemas

**Error: "Error al conectar con el servidor"**
- Verifica que la API REST esté corriendo en `http://localhost:3000`
- Revisa la configuración en `.env`

**Error: "Credenciales inválidas"**
- Verifica que el usuario exista en la base de datos
- Asegúrate de usar el correo y contraseña correctos

**Error: "Token inválido o expirado"**
- El token JWT tiene una expiración de 1 hora
- Vuelve a iniciar sesión

