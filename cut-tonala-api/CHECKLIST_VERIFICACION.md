# ✅ Checklist Rápido de Verificación

## 🔧 ANTES DE GRABAR - Verificar que todo funciona

### 1. Base de Datos (2 minutos)
```sql
-- Verificar que existe la tabla usuarios
SHOW TABLES LIKE 'usuarios';

-- Verificar estructura
DESCRIBE usuarios;

-- Verificar que hay usuarios
SELECT id, correo, rol FROM usuarios;
```

**✅ Debe mostrar:**
- Tabla `usuarios` existe
- Campos: `id`, `correo`, `contraseña`, `rol`
- Al menos 2 usuarios (1 admin, 1 cliente)

---

### 2. Servidor (30 segundos)
```bash
node app.js
```

**✅ Debe mostrar:**
```
========================================
 Servidor corriendo en puerto 3000
 http://localhost:3000
 API disponible en http://localhost:3000/api
 Autenticación JWT activada
========================================
```

---

### 3. Postman - Configuración (1 minuto)

1. **Importar colección**: `CUT_Tonala_API.postman_collection.json`
2. **Verificar variables**:
   - `base_url` = `http://localhost:3000`
   - `token` = (vacío al inicio)

---

### 4. Pruebas Rápidas (5 minutos)

#### ✅ Test 1: Login funciona
- **Request**: `POST /api/login`
- **Body**: 
  ```json
  {
    "correo": "admin@test.com",
    "contraseña": "password123"
  }
  ```
- **Esperado**: `200 OK` con `{ "token": "..." }`

#### ✅ Test 2: Sin token = Error 401
- **Request**: `GET /api/alumnos` (sin header Authorization)
- **Esperado**: `401 Unauthorized`

#### ✅ Test 3: Con token = Acceso permitido
- **Request**: `GET /api/alumnos` (con `Authorization: Bearer {{token}}`)
- **Esperado**: `200 OK` con array de alumnos

#### ✅ Test 4: Cliente no puede crear
- **Login como cliente**: `POST /api/login` con `cliente@test.com`
- **Request**: `POST /api/alumnos` (con token de cliente)
- **Esperado**: `403 Forbidden`

#### ✅ Test 5: Admin sí puede crear
- **Login como admin**: `POST /api/login` con `admin@test.com`
- **Request**: `POST /api/alumnos` (con token de admin)
- **Esperado**: `201 Created`

---

## 🚨 Si Algo No Funciona

### Error: "Token requerido" pero sí tengo token
- **Solución**: Verifica que el header sea exactamente `Authorization: Bearer {{token}}`
- Verifica que la variable `token` tenga valor en Postman

### Error: "Credenciales inválidas"
- **Solución**: Verifica que el usuario existe en la BD
- Verifica que la contraseña sea correcta
- Crea el usuario de nuevo: `node scripts/crear_usuario.js admin@test.com password123 admin`

### Error: "Error en la base de datos"
- **Solución**: Verifica que la tabla `usuarios` existe
- Verifica la conexión a la BD en `.env`

### Error: "Token inválido"
- **Solución**: Haz login de nuevo para obtener un token fresco
- Verifica que `JWT_SECRET` esté en `.env`

---

## 📋 Orden de Pruebas para el Video

1. ✅ **Sin token** → Error 401
2. ✅ **Login** → Obtener token
3. ✅ **Con token** → Acceso permitido
4. ✅ **Login cliente** → Token de cliente
5. ✅ **Cliente crea** → Error 403
6. ✅ **Login admin** → Token de admin
7. ✅ **Admin crea** → Éxito 201
8. ✅ **Credenciales incorrectas** → Error 401
9. ✅ **Token inválido** → Error 403

---

## ⏱️ Tiempo Total de Verificación: ~10 minutos

¡Listo para grabar! 🎬

