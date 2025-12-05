# 🎥 Guía para Demostración en Video - Autenticación JWT

## 📋 Orden de Demostración Recomendado

### **PASO 1: Preparación (Antes de grabar)**

1. ✅ Verificar que el servidor esté corriendo
   ```bash
   node app.js
   ```
   Deberías ver: `Servidor corriendo en puerto 3000`

2. ✅ Verificar que la tabla `usuarios` existe
   - Abre tu cliente MySQL (phpMyAdmin, MySQL Workbench, etc.)
   - Verifica que existe la tabla `usuarios` con los campos correctos

3. ✅ Crear usuarios de prueba
   ```bash
   node scripts/crear_usuario.js admin@test.com password123 admin
   node scripts/crear_usuario.js cliente@test.com password123 cliente
   ```

4. ✅ Abrir Postman y importar la colección
   - Importa `CUT_Tonala_API.postman_collection.json`
   - Verifica que `base_url` = `http://localhost:3000`

---

## 🎬 DEMOSTRACIÓN EN VIDEO

### **PARTE 1: Mostrar que las rutas están protegidas (2 minutos)**

#### 1.1 Intentar acceder SIN token
1. En Postman, selecciona: **Alumnos → Obtener todos los alumnos**
2. **Quita** el header `Authorization` (si está)
3. Click en **Send**
4. **Resultado esperado**: 
   - Status: `401 Unauthorized`
   - Body: `{ "error": "Token requerido. Inicia sesión primero." }`
5. **Dice en el video**: *"Como pueden ver, sin autenticación no podemos acceder a los recursos protegidos"*

#### 1.2 Intentar crear alumno SIN token
1. Selecciona: **Alumnos → Crear nuevo alumno**
2. Asegúrate de que NO tenga header `Authorization`
3. Click en **Send**
4. **Resultado esperado**: `401 Unauthorized`
5. **Dice**: *"Tampoco podemos crear recursos sin autenticación"*

---

### **PARTE 2: Login y obtención de token (2 minutos)**

#### 2.1 Login como ADMIN
1. Selecciona: **Autenticación → Login**
2. El body ya tiene:
   ```json
   {
     "correo": "admin@test.com",
     "contrasena": "password123"
   }
   ```
3. Click en **Send**
4. **Resultado esperado**:
   - Status: `200 OK`
   - Body: `{ "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." }`
5. **Dice**: *"Al hacer login, recibimos un token JWT que usaremos para autenticarnos"*
6. **Muestra** que el token se guardó automáticamente:
   - Ve a la colección → Variables
   - Muestra que `token` tiene el valor del token

#### 2.2 Verificar que ahora SÍ podemos acceder
1. Selecciona: **Alumnos → Obtener todos los alumnos**
2. **Verifica** que tiene el header `Authorization: Bearer {{token}}`
3. Click en **Send**
4. **Resultado esperado**:
   - Status: `200 OK`
   - Body: Array con alumnos
5. **Dice**: *"Ahora con el token, podemos acceder a los recursos protegidos"*

---

### **PARTE 3: Demostrar autorización por roles (3 minutos)**

#### 3.1 Login como CLIENTE
1. Selecciona: **Autenticación → Login - Cliente**
2. Click en **Send**
3. **Resultado esperado**: `200 OK` con nuevo token
4. **Dice**: *"Ahora voy a iniciar sesión como cliente para demostrar los diferentes permisos"*

#### 3.2 Cliente intenta CREAR alumno (debe fallar)
1. Selecciona: **Alumnos → Crear nuevo alumno**
2. El header `Authorization` ya tiene el token del cliente
3. Click en **Send**
4. **Resultado esperado**:
   - Status: `403 Forbidden`
   - Body: `{ "error": "Acceso restringido solo para administradores" }`
5. **Dice**: *"Como pueden ver, un cliente no puede crear alumnos, solo los administradores tienen ese permiso"*

#### 3.3 Cliente intenta ELIMINAR alumno (debe fallar)
1. Selecciona: **Alumnos → Eliminar alumno**
2. Cambia `:id` por un ID existente (ej: `1`)
3. Click en **Send**
4. **Resultado esperado**: `403 Forbidden`
5. **Dice**: *"Tampoco puede eliminar recursos, solo los administradores pueden hacerlo"*

#### 3.4 Cliente SÍ puede VER alumnos
1. Selecciona: **Alumnos → Obtener todos los alumnos**
2. Click en **Send**
3. **Resultado esperado**: `200 OK` con lista de alumnos
4. **Dice**: *"Pero sí puede ver los recursos, ya que solo requiere autenticación, no rol de admin"*

#### 3.5 Volver a login como ADMIN y demostrar permisos
1. Selecciona: **Autenticación → Login** (admin)
2. Click en **Send`
3. Ahora intenta **Crear nuevo alumno**
4. **Resultado esperado**: `201 Created`
5. **Dice**: *"Ahora como administrador, sí puedo crear recursos"*

---

### **PARTE 4: Demostrar errores de autenticación (2 minutos)**

#### 4.1 Login con credenciales incorrectas
1. Selecciona: **Autenticación → Login**
2. Cambia el body a:
   ```json
   {
     "correo": "admin@test.com",
     "contrasena": "contrasena_incorrecta"
   }
   ```
3. Click en **Send**
4. **Resultado esperado**:
   - Status: `401 Unauthorized`
   - Body: `{ "error": "Credenciales inválidas" }`
5. **Dice**: *"Si las credenciales son incorrectas, recibimos un error 401"*

#### 4.2 Login sin campos requeridos
1. En el body del Login, deja solo:
   ```json
   {
     "correo": "admin@test.com"
   }
   ```
2. Click en **Send**
3. **Resultado esperado**:
   - Status: `400 Bad Request`
   - Body: `{ "error": "Se requieren correo y contraseña" }`
4. **Dice**: *"También validamos que todos los campos requeridos estén presentes"*

#### 4.3 Token inválido o expirado
1. En cualquier ruta protegida, cambia el header `Authorization` a:
   ```
   Bearer token_invalido_12345
   ```
2. Click en **Send**
3. **Resultado esperado**:
   - Status: `403 Forbidden`
   - Body: `{ "error": "Token inválido o expirado" }`
4. **Dice**: *"Si el token es inválido o ha expirado, recibimos un error 403"*

---

### **PARTE 5: Resumen y conclusiones (1 minuto)**

**Dice en el video**:
- *"Hemos implementado un sistema completo de autenticación y autorización con JWT"*
- *"Las rutas están protegidas según el rol del usuario"*
- *"Los administradores tienen permisos completos, mientras que los clientes solo pueden ver recursos"*
- *"Todos los errores están manejados correctamente con códigos HTTP apropiados"*

---

## 📝 Checklist Antes de Grabar

- [ ] Servidor corriendo (`node app.js`)
- [ ] Tabla `usuarios` creada en la base de datos
- [ ] Usuarios de prueba creados (admin y cliente)
- [ ] Postman abierto con la colección importada
- [ ] Variable `base_url` configurada
- [ ] Al menos 1 alumno y 1 taller en la base de datos (para pruebas)
- [ ] `.env` tiene `JWT_SECRET` configurado

---

## 🎯 Puntos Clave a Mencionar

1. ✅ **Autenticación**: "Las rutas requieren un token JWT válido"
2. ✅ **Autorización**: "Diferentes roles tienen diferentes permisos"
3. ✅ **Seguridad**: "Las contraseñas están cifradas con bcrypt"
4. ✅ **Validación**: "Validamos credenciales y campos requeridos"
5. ✅ **Manejo de errores**: "Errores claros con códigos HTTP apropiados"

---

## ⏱️ Tiempo Estimado

- **Parte 1**: 2 minutos
- **Parte 2**: 2 minutos
- **Parte 3**: 3 minutos
- **Parte 4**: 2 minutos
- **Parte 5**: 1 minuto
- **Total**: ~10 minutos (dentro del límite de 5 minutos, ajusta según necesidad)

---

## 💡 Tips para la Grabación

1. **Habla claro y pausado**
2. **Muestra la pantalla completa** (Postman + terminal del servidor)
3. **Incluye fecha y hora** en la pantalla
4. **Resalta los resultados** (status codes, respuestas JSON)
5. **Explica cada paso** antes de ejecutarlo
6. **Si algo falla**, muestra el error y explica por qué

---

## 🚨 Si Algo Sale Mal Durante la Grabación

- **Error 401**: "El token no está configurado, déjenme hacer login primero"
- **Error 500**: "Hay un problema con la base de datos, déjenme verificar"
- **Token no se guarda**: "Déjenme configurar el token manualmente en las variables"

---

¡Listo para grabar! 🎬

