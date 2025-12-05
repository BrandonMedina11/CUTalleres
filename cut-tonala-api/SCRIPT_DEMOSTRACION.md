# 📜 Script de Demostración - Autenticación JWT

## 🎬 INTRODUCCIÓN (30 segundos)

"Hola, en este video voy a demostrar la implementación de autenticación y autorización con JWT en nuestra API REST del CUT Tonalá.

Voy a mostrar:
1. Cómo las rutas están protegidas
2. El proceso de login y obtención de token
3. Los diferentes permisos según el rol del usuario
4. El manejo de errores de autenticación"

---

## 📍 PARTE 1: Rutas Protegidas (1 minuto)

"Primero, voy a mostrar que las rutas están protegidas. Intentaré acceder a los alumnos sin autenticación."

**[Hacer request sin token]**

"Como pueden ver, recibimos un error 401 Unauthorized que dice 'Token requerido'. Esto demuestra que la ruta está protegida y requiere autenticación."

---

## 🔐 PARTE 2: Login (1 minuto)

"Ahora voy a hacer login para obtener un token JWT. Usaré las credenciales de administrador."

**[Hacer login]**

"Perfecto, recibimos un token JWT. Este token se guarda automáticamente en Postman y lo usaremos para autenticarnos en las siguientes peticiones."

"Ahora, con el token, puedo acceder a los recursos protegidos."

**[Hacer GET alumnos con token]**

"Excelente, ahora sí recibimos los datos. El token nos permite autenticarnos correctamente."

---

## 👥 PARTE 3: Autorización por Roles (2 minutos)

"Ahora voy a demostrar la autorización por roles. Primero, iniciaré sesión como cliente."

**[Login como cliente]**

"Ahora intentaré crear un alumno como cliente."

**[Intentar crear alumno como cliente]**

"Como pueden ver, recibimos un error 403 Forbidden que dice 'Acceso restringido solo para administradores'. Esto demuestra que los clientes no pueden crear recursos."

"Sin embargo, los clientes sí pueden ver los recursos."

**[GET alumnos como cliente]**

"Perfecto, pueden ver pero no modificar."

"Ahora volveré a iniciar sesión como administrador para mostrar los permisos completos."

**[Login como admin]**

"Como administrador, sí puedo crear recursos."

**[Crear alumno como admin]**

"Excelente, recibimos 201 Created. Los administradores tienen permisos completos."

---

## ⚠️ PARTE 4: Manejo de Errores (1 minuto)

"Finalmente, voy a mostrar el manejo de errores. Primero, credenciales incorrectas."

**[Login con contrasena incorrecta]**

"Recibimos 401 Unauthorized con el mensaje 'Credenciales inválidas'."

"Ahora, intentaré usar un token inválido."

**[Request con token inválido]**

"Recibimos 403 Forbidden indicando que el token es inválido o expirado."

---

## ✅ CONCLUSIÓN (30 segundos)

"En resumen, hemos implementado:
- Autenticación con JWT
- Autorización por roles (admin y cliente)
- Protección de rutas
- Manejo adecuado de errores

El sistema está completamente funcional y seguro. Gracias por ver el video."

---

## 🎯 Puntos Clave a Enfatizar

- ✅ "Las rutas están protegidas"
- ✅ "El token se genera y guarda automáticamente"
- ✅ "Diferentes roles tienen diferentes permisos"
- ✅ "Los errores están manejados correctamente"

