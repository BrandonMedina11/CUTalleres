# 📋 Documentación de la Actividad 11

## Interfaz Administrativa en Laravel consumiendo API REST con Autenticación

### 🎯 Objetivo

Desarrollar una interfaz administrativa utilizando Laravel (estructura simplificada), mediante la cual se consuman los servicios de una API REST previamente implementada, asegurándose de incluir mecanismos de autenticación que protejan el acceso a los recursos según el tipo de usuario autorizado.

---

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

---

## 🔧 Implementación por Etapas

### 1. Preparación del Proyecto Laravel

Se creó un proyecto Laravel con estructura simplificada que incluye:

- **Composer.json**: Configuración de dependencias (Guzzle HTTP)
- **Estructura de carpetas**: Siguiendo convenciones de Laravel
- **Configuración**: Archivos de configuración para API y aplicación

**Archivos creados:**
- `composer.json` - Dependencias del proyecto
- `config/app.php` - Configuración de la aplicación
- `config/api.php` - Configuración de la API REST

### 2. Servicio Laravel para consumir la API REST

Se implementó `ApiService.php` que actúa como cliente HTTP para consumir la API REST:

**Características:**
- ✅ Cliente HTTP usando Guzzle
- ✅ Manejo automático de tokens JWT
- ✅ Métodos GET, POST, PUT, DELETE
- ✅ Manejo de errores y tokens expirados

**Ubicación:** `app/Services/ApiService.php`

**Métodos principales:**
```php
- get($endpoint, $params = [])      // Petición GET
- post($endpoint, $data = [])       // Petición POST
- put($endpoint, $data = [])        // Petición PUT
- delete($endpoint)                 // Petición DELETE
```

### 3. Componente Laravel para mostrar lista de talleres

Se creó `TallerController.php` que consume el servicio de API:

**Funcionalidades:**
- ✅ Obtener lista de talleres desde la API
- ✅ Mostrar detalles de un taller específico
- ✅ Manejo de errores y redirecciones

**Ubicación:** `app/Http/Controllers/TallerController.php`

**Vista:** `resources/views/talleres/index.blade.php`
- Muestra lista de talleres con tarjetas
- Incluye imágenes de talleres
- Información de categoría y profesor
- Diseño responsive con Bootstrap 5

### 4. Servicio y componente para autenticación de administrador

Se implementó `AuthController.php` para manejar la autenticación:

**Funcionalidades:**
- ✅ Formulario de login con validación
- ✅ Autenticación contra API REST (`POST /api/login`)
- ✅ Almacenamiento de token JWT en sesión
- ✅ Cerrar sesión

**Ubicación:** `app/Http/Controllers/AuthController.php`

**Vista:** `resources/views/auth/login.blade.php`
- Formulario reactivo con validación
- Campos: correo y contraseña
- Manejo de errores

### 5. Protección de rutas con middleware (AuthGuard)

Se implementó `AuthMiddleware.php` para proteger rutas:

**Funcionalidades:**
- ✅ Verificar existencia de token en sesión
- ✅ Redirigir al login si no hay token
- ✅ Proteger rutas administrativas

**Ubicación:** `app/Http/Middleware/AuthMiddleware.php`

**Aplicación en rutas:**
```php
// Rutas protegidas
Route::middleware(['AuthMiddleware'])->group(function() {
    Route::get('/talleres', ...);
    Route::get('/talleres/{id}', ...);
});
```

### 6. Validación del token en peticiones HTTP

El `ApiService` automáticamente adjunta el token JWT en el header `Authorization` de cada petición:

**Implementación:**
```php
protected function getHeaders()
{
    $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    $token = $this->getToken(); // Obtiene de sesión
    if ($token) {
        $headers['Authorization'] = 'Bearer ' . $token;
    }

    return $headers;
}
```

**Manejo de tokens expirados:**
- Si la API responde con 401 o 403, se limpia la sesión
- El usuario es redirigido al login automáticamente

### 7. Pruebas y despliegue

**Pruebas realizadas:**

✅ **Iniciar sesión como administrador**
- Formulario de login funcional
- Validación de campos
- Autenticación exitosa contra API
- Almacenamiento de token en sesión

✅ **Acceder y mostrar talleres con imágenes**
- Lista de talleres se carga correctamente
- Imágenes se muestran desde la API
- Información completa de cada taller
- Vista de detalles funcional

✅ **Probar bloqueo de acceso sin autenticación**
- Rutas protegidas redirigen al login
- Sin token no se puede acceder a recursos
- Middleware funciona correctamente

---

## 🔐 Flujo de Autenticación

1. **Usuario accede a ruta protegida** → Redirigido a `/login`
2. **Usuario ingresa credenciales** → `POST /login`
3. **Laravel llama a API REST** → `POST http://localhost:3000/api/login`
4. **API valida credenciales** → Retorna token JWT
5. **Laravel almacena token en sesión** → `$_SESSION['api_token']`
6. **Usuario accede a recursos** → Token se envía automáticamente en headers
7. **API valida token** → Retorna datos solicitados

---

## 📡 Endpoints de la API Consumidos

### Autenticación
- `POST /api/login`
  - Body: `{ "correo": "...", "contrasena": "..." }`
  - Response: `{ "token": "...", "usuario": {...} }`

### Talleres
- `GET /api/talleres` (requiere token)
  - Headers: `Authorization: Bearer {token}`
  - Response: `[{ "id": 1, "nombre": "...", ... }]`

- `GET /api/talleres/{id}` (requiere token)
  - Headers: `Authorization: Bearer {token}`
  - Response: `{ "id": 1, "nombre": "...", ... }`

---

## 🎨 Características de la Interfaz

### Diseño
- ✅ Bootstrap 5 para estilos
- ✅ Bootstrap Icons para iconos
- ✅ Diseño responsive
- ✅ Gradientes y efectos modernos
- ✅ Tarjetas con hover effects

### Funcionalidades
- ✅ Formularios con validación
- ✅ Mensajes de éxito/error
- ✅ Navegación intuitiva
- ✅ Visualización de imágenes
- ✅ Información estructurada

---

## 🛠️ Tecnologías Utilizadas

- **PHP 8.1+**: Lenguaje de programación
- **Laravel (estructura simplificada)**: Framework PHP
- **Guzzle HTTP**: Cliente HTTP para consumir API REST
- **Blade (simplificado)**: Motor de plantillas
- **Bootstrap 5**: Framework CSS
- **Bootstrap Icons**: Iconos
- **Sesiones PHP**: Almacenamiento de tokens

---

## 📝 Notas de Implementación

### Diferencias con Angular (actividad original)

La actividad original estaba diseñada para Angular, pero se adaptó a Laravel:

1. **Servicios**: En lugar de servicios Angular, se usan clases PHP (`ApiService`)
2. **Componentes**: En lugar de componentes Angular, se usan controladores Laravel
3. **Vistas**: En lugar de templates Angular, se usan vistas Blade
4. **Routing**: En lugar de Angular Router, se usa sistema de routing PHP
5. **Interceptors**: En lugar de HTTP Interceptors, el `ApiService` maneja los headers automáticamente
6. **Guards**: En lugar de Route Guards, se usa middleware de Laravel

### Adaptaciones realizadas

- **Productos → Talleres**: Se adaptó para usar el recurso "talleres" que existe en la API
- **Estructura simplificada**: Se creó una estructura similar a Laravel pero sin todas las dependencias
- **Motor Blade simplificado**: Se implementó un compilador básico de Blade

---

## ✅ Checklist de Verificación

- [x] Proyecto Laravel creado con estructura adecuada
- [x] Servicio para consumir API REST implementado
- [x] Controlador de autenticación funcional
- [x] Controlador de talleres funcional
- [x] Vistas Blade creadas (login, lista, detalles)
- [x] Middleware de autenticación implementado
- [x] Rutas protegidas con middleware
- [x] Token JWT se envía automáticamente en peticiones
- [x] Manejo de errores implementado
- [x] Interfaz moderna y responsive
- [x] Documentación completa

---

## 🚀 Instrucciones de Uso

1. **Instalar dependencias:**
   ```bash
   cd admin-app
   composer install
   ```

2. **Configurar variables de entorno:**
   - Editar `.env` si es necesario
   - Verificar `API_BASE_URL=http://localhost:3000`

3. **Iniciar servidor:**
   ```bash
   cd admin-app/public
   php -S localhost:8000
   ```

4. **Acceder a la aplicación:**
   - Abrir navegador en `http://localhost:8000`
   - Iniciar sesión con credenciales válidas
   - Explorar la interfaz administrativa

---

## 📚 Archivos de Documentación

- `README.md` - Documentación general del proyecto
- `GUIA_INSTALACION.md` - Guía detallada de instalación
- `DOCUMENTACION_ACTIVIDAD.md` - Este archivo

---

## 🎓 Conclusión

Se ha desarrollado exitosamente una interfaz administrativa en Laravel que consume una API REST con autenticación JWT. La aplicación incluye:

- ✅ Autenticación completa
- ✅ Protección de rutas
- ✅ Consumo de API REST
- ✅ Interfaz moderna y funcional
- ✅ Manejo de errores robusto
- ✅ Documentación completa

La implementación cumple con todos los requisitos de la actividad, adaptando la estructura original de Angular a Laravel de manera funcional y educativa.

