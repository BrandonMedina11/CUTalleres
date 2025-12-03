# 🚀 Instrucciones Rápidas

## Instalación en 3 pasos

### 1. Instalar dependencias
```bash
cd admin-app
composer install
```

### 2. Verificar que la API esté corriendo
```bash
# En otra terminal, en el directorio raíz
npm start
```

### 3. Iniciar servidor
```bash
cd admin-app/public
php -S localhost:8000
```

## Acceder a la aplicación

Abre tu navegador en: **http://localhost:8000**

## Credenciales de prueba

Si no tienes un usuario, créalo:
```bash
# En el directorio raíz del proyecto
node scripts/crear_usuario.js admin@test.com password123 admin
```

Luego usa:
- **Correo:** `admin@test.com`
- **Contraseña:** `password123`

## Estructura de archivos importantes

- `app/Services/ApiService.php` - Servicio para consumir API
- `app/Http/Controllers/AuthController.php` - Controlador de login
- `app/Http/Controllers/TallerController.php` - Controlador de talleres
- `app/Http/Middleware/AuthMiddleware.php` - Protección de rutas
- `routes/web.php` - Definición de rutas
- `resources/views/` - Vistas Blade

## Solución rápida de problemas

**Error de conexión:** Verifica que la API esté en `http://localhost:3000`

**Error 404:** Asegúrate de estar en el directorio `public` al iniciar el servidor

**Token expirado:** Cierra sesión y vuelve a iniciar sesión

