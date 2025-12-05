# 🎥 Script para Video - Solución del Problema de Login

## 🎬 INTRODUCCIÓN (30 segundos)

"Hola, en este video voy a explicar cómo solucionamos el problema de 'Credenciales inválidas' que aparecía al intentar hacer login en nuestra API.

El problema tenía dos causas principales:
1. El código estaba buscando en la tabla incorrecta
2. Había una discrepancia en el nombre del campo de contraseña

Voy a mostrar cómo funciona ahora el sistema de autenticación."

---

## 🔍 PARTE 1: Explicar el Problema (1 minuto)

"Primero, déjenme mostrarles el problema que teníamos. Al intentar hacer login con credenciales correctas, recibíamos un error 401 'Credenciales inválidas'."

**[Mostrar Postman con el error]**

"El problema era que:
- El código estaba buscando usuarios en la tabla `administradores` con el campo `email`
- Pero nuestro sistema de autenticación usa la tabla `usuarios` con el campo `correo`
- Además, había una discrepancia: el código esperaba `contrasena` (sin ñ) pero algunos lugares usaban `contraseña` (con ñ)"

---

## ✅ PARTE 2: La Solución (1 minuto)

"Solucionamos esto haciendo tres cambios principales:

1. **Cambiamos la consulta** para buscar en la tabla `usuarios` en lugar de `administradores`
2. **Corregimos el campo** para usar `correo` y `contrasena` (sin ñ) según la estructura real de nuestra base de datos
3. **Actualizamos el código** para que acepte ambos formatos de contraseña para mayor compatibilidad"

**[Mostrar el código corregido brevemente]**

---

## 🧪 PARTE 3: Demostración Funcionando (2 minutos)

"Ahora voy a demostrar que funciona correctamente. Primero, voy a crear un usuario de prueba."

**[Opcional: Mostrar el script de creación]**

"Ahora voy a hacer login con las credenciales correctas."

**[Hacer login en Postman]**

"Perfecto! Ahora recibimos un token JWT exitosamente. El login funciona correctamente."

**[Mostrar la respuesta con el token]**

"El token se guarda automáticamente en Postman y ahora podemos usarlo para acceder a las rutas protegidas."

**[Hacer GET alumnos con el token]**

"Excelente, ahora sí podemos acceder a los recursos protegidos con el token."

---

## 📊 PARTE 4: Verificación (1 minuto)

"Para verificar que todo está correcto, tenemos usuarios en la base de datos:"

**[Mostrar los usuarios existentes]**

"Tenemos:
- `admin@cutonala.mx` con rol admin
- `alumno@cutonala.mx` con rol alumno  
- `profesor@cutonala.mx` con rol profesor
- Y el usuario de prueba `admin@test.com` que acabamos de crear"

"Todos estos usuarios pueden hacer login correctamente ahora."

---

## ✅ CONCLUSIÓN (30 segundos)

"En resumen, el problema estaba en:
- La tabla y campos incorrectos en la consulta SQL
- La discrepancia en el nombre del campo de contraseña

Ahora el sistema funciona correctamente:
- ✅ Login con usuarios de la tabla `usuarios`
- ✅ Validación correcta de contraseñas con bcrypt
- ✅ Generación de tokens JWT
- ✅ Acceso a rutas protegidas

El sistema de autenticación está completamente funcional. Gracias por ver el video."

---

## 🎯 Puntos Clave a Mencionar

1. **Problema identificado**: "Buscaba en la tabla incorrecta y con campos incorrectos"
2. **Solución aplicada**: "Corregimos la consulta SQL y los nombres de campos"
3. **Resultado**: "Ahora el login funciona correctamente con todos los usuarios"
4. **Verificación**: "Todos los usuarios pueden autenticarse exitosamente"

---

## 💡 Tips para la Grabación

- Muestra el código corregido brevemente (no demasiado detalle)
- Enfócate en la demostración práctica en Postman
- Muestra claramente el antes (error) y el después (funcionando)
- Habla claro y pausado
- Resalta los resultados exitosos (200 OK, token generado)

---

## ⏱️ Tiempo Total: ~5 minutos

- Introducción: 30 seg
- Problema: 1 min
- Solución: 1 min
- Demostración: 2 min
- Conclusión: 30 seg

¡Listo para grabar! 🎬

