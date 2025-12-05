# 📚 Guía de Instalación y Uso - Interfaz Administrativa Laravel

## 🎯 Objetivo

Esta guía te ayudará a instalar y ejecutar la interfaz administrativa desarrollada en Laravel (estructura simplificada) que consume la API REST de Node.js.

## 📋 Requisitos Previos

1. **PHP 8.1 o superior** instalado
2. **Composer** instalado ([https://getcomposer.org/](https://getcomposer.org/))
3. **API REST de Node.js** corriendo en `http://localhost:3000`
4. **Servidor web** (Apache/Nginx) o PHP Built-in Server

## 🚀 Pasos de Instalación

### Paso 1: Instalar Dependencias

```bash
cd admin-app
composer install
```

Esto instalará:
- `guzzlehttp/guzzle` - Para hacer peticiones HTTP a la API
- `illuminate/support` - Componentes de Laravel

### Paso 2: Configurar Variables de Entorno

El archivo `.env` ya está creado con valores por defecto. Si necesitas cambiarlos:

```env
API_BASE_URL=http://localhost:3000
API_TIMEOUT=30
```

### Paso 3: Verificar que la API esté Corriendo

Asegúrate de que la API REST de Node.js esté corriendo:

```bash
# En el directorio raíz del proyecto (cut-tonala-api)
npm start
```

Deberías ver:
```
Servidor corriendo en puerto 3000
http://localhost:3000
```

### Paso 4: Iniciar el Servidor de Desarrollo

#### Opción A: PHP Built-in Server (Recomendado para desarrollo)

```bash
cd admin-app/public
php -S localhost:8000
```

#### Opción B: Apache/Nginx

Configura tu servidor web para que apunte al directorio `admin-app/public`

### Paso 5: Acceder a la Aplicación

Abre tu navegador y ve a:
```
http://localhost:8000
```

Serás redirigido automáticamente al login.

## 🔐 Credenciales de Prueba

Para poder iniciar sesión, necesitas tener un usuario en la base de datos. Puedes crear uno usando el script:

```bash
# En el directorio raíz del proyecto
node scripts/crear_usuario.js admin@test.com password123 admin
```

Luego usa estas credenciales:
- **Correo:** `admin@test.com`
- **Contraseña:** `password123`

## 📖 Uso de la Aplicación

### 1. Iniciar Sesión

1. Accede a `http://localhost:8000/login`
2. Ingresa tu correo y contraseña
3. Haz clic en "Iniciar Sesión"

### 2. Ver Lista de Talleres

Después del login, serás redirigido automáticamente a la lista de talleres donde podrás ver:
- Nombre del taller
- Descripción
- Categoría
- Profesor
- Imagen (si está disponible)

### 3. Ver Detalles de un Taller

Haz clic en "Ver Detalles" de cualquier taller para ver la información completa.

### 4. Cerrar Sesión

Haz clic en "Salir" en el menú superior para cerrar sesión.

## 🛡️ Protección de Rutas

Las siguientes rutas están protegidas y requieren autenticación:
- `/` (raíz)
- `/talleres`
- `/talleres/{id}`

Si intentas acceder sin estar autenticado, serás redirigido al login.

## 🧪 Pruebas

### Prueba 1: Autenticación
1. Intenta acceder a `/talleres` sin iniciar sesión
2. Deberías ser redirigido al login
3. Inicia sesión con credenciales válidas
4. Deberías poder acceder a la lista de talleres

### Prueba 2: Token Expirado
1. Inicia sesión
2. Espera 1 hora (o modifica el token manualmente)
3. Intenta acceder a una ruta protegida
4. Deberías ser redirigido al login

### Prueba 3: Credenciales Inválidas
1. Intenta iniciar sesión con credenciales incorrectas
2. Deberías ver un mensaje de error

## 🐛 Solución de Problemas

### Error: "Error al conectar con el servidor"

**Causa:** La API REST no está corriendo o la URL está incorrecta.

**Solución:**
1. Verifica que la API esté corriendo: `http://localhost:3000`
2. Revisa el archivo `.env` y asegúrate de que `API_BASE_URL` sea correcto
3. Verifica que no haya un firewall bloqueando la conexión

### Error: "Credenciales inválidas"

**Causa:** El usuario no existe o la contraseña es incorrecta.

**Solución:**
1. Verifica que el usuario exista en la base de datos
2. Crea un nuevo usuario si es necesario:
   ```bash
   node scripts/crear_usuario.js nuevo@usuario.com password123 admin
   ```

### Error: "Token inválido o expirado"

**Causa:** El token JWT ha expirado (válido por 1 hora).

**Solución:**
1. Cierra sesión y vuelve a iniciar sesión
2. El token se renovará automáticamente

### Error 404: "Página no encontrada"

**Causa:** El servidor web no está configurado correctamente.

**Solución:**
1. Si usas PHP Built-in Server, asegúrate de estar en el directorio `public`
2. Si usas Apache, verifica que el `.htaccess` esté funcionando
3. Verifica que todas las rutas estén definidas en `routes/web.php`

## 📝 Notas Importantes

1. **Sesiones:** Las sesiones se almacenan en archivos PHP por defecto. Asegúrate de que el directorio tenga permisos de escritura.

2. **CORS:** La API debe tener CORS habilitado para permitir peticiones desde el navegador.

3. **Tokens JWT:** Los tokens tienen una expiración de 1 hora. Después de ese tiempo, necesitarás volver a iniciar sesión.

4. **Seguridad:** Esta es una implementación simplificada para fines educativos. En producción, se recomienda:
   - Usar HTTPS
   - Implementar CSRF tokens más robustos
   - Usar Laravel completo con todas sus características de seguridad

## 📚 Recursos Adicionales

- Documentación de la API: `GUIA_AUTENTICACION.md`
- Documentación de Guzzle: [https://docs.guzzlephp.org/](https://docs.guzzlephp.org/)
- Documentación de Laravel: [https://laravel.com/docs](https://laravel.com/docs)

## ✅ Checklist de Verificación

- [ ] PHP 8.1+ instalado
- [ ] Composer instalado
- [ ] Dependencias instaladas (`composer install`)
- [ ] API REST corriendo en `http://localhost:3000`
- [ ] Usuario de prueba creado en la base de datos
- [ ] Servidor web configurado
- [ ] Aplicación accesible en `http://localhost:8000`
- [ ] Login funcionando
- [ ] Lista de talleres visible
- [ ] Protección de rutas funcionando

