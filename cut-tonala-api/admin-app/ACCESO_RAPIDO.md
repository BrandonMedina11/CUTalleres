# 🚀 Acceso Rápido a la Aplicación

## ✅ Estado Actual

Todo está configurado y funcionando correctamente:
- ✅ Servidor PHP corriendo en `http://localhost:8000`
- ✅ API REST accesible en `http://localhost:3000`
- ✅ Todas las dependencias instaladas
- ✅ Configuración completa

## 🌐 Acceder a la Aplicación

1. **Abre tu navegador** y ve a:
   ```
   http://localhost:8000
   ```

2. **Serás redirigido automáticamente al login**

3. **Inicia sesión** con tus credenciales:
   - Si no tienes un usuario, créalo ejecutando:
     ```bash
     # En el directorio raíz del proyecto (cut-tonala-api)
     node scripts/crear_usuario.js admin@test.com password123 admin
     ```
   - Luego usa:
     - **Correo:** `admin@test.com`
     - **Contraseña:** `password123`

## 📋 Rutas Disponibles

- `http://localhost:8000/login` - Página de login
- `http://localhost:8000/talleres` - Lista de talleres (requiere login)
- `http://localhost:8000/talleres/{id}` - Detalles de un taller (requiere login)

## ⚠️ Nota sobre el Warning de MongoDB

El warning que aparece sobre MongoDB no es un problema. Es solo una extensión que PHP intenta cargar pero no está instalada. **No afecta la funcionalidad de la aplicación** ya que no usamos MongoDB.

## 🛠️ Si el Servidor se Detiene

Si necesitas reiniciar el servidor:

```bash
cd admin-app/public
php -S localhost:8000
```

## ✅ Verificación Rápida

Si quieres verificar que todo esté bien, ejecuta:

```bash
cd admin-app
php verificar.php
```

