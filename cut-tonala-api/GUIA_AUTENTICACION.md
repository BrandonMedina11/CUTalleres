# 🔐 Guía de Implementación: Autenticación y Autorización JWT

## 📋 Pasos de Implementación

### 1. Crear la tabla de usuarios

Ejecuta el script SQL en tu base de datos:

```sql
-- Ejecutar: database/usuarios_table.sql
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  correo VARCHAR(100) NOT NULL UNIQUE,
  contraseña VARCHAR(255) NOT NULL,
  rol ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Verificar dependencias

Las dependencias ya están instaladas:
- ✅ `jsonwebtoken`
- ✅ `bcrypt`

Si necesitas instalarlas:
```bash
npm install jsonwebtoken bcrypt
```

### 3. Configurar variable de entorno

Asegúrate de tener en tu archivo `.env`:

```env
JWT_SECRET=mitokenseguro123
```

**⚠️ IMPORTANTE**: En producción, usa un secreto más seguro y largo.

### 4. Crear usuario de prueba

#### Opción A: Usando el script Node.js
```bash
node scripts/crear_usuario.js admin@test.com password123 admin
node scripts/crear_usuario.js cliente@test.com password123 cliente
```

#### Opción B: Manualmente en MySQL
```sql
-- Primero, genera el hash de la contraseña usando Node.js:
-- const bcrypt = require('bcrypt');
-- const hash = await bcrypt.hash('password123', 10);
-- console.log(hash);

-- Luego inserta:
INSERT INTO usuarios (correo, contraseña, rol) VALUES 
('admin@test.com', '$2b$10$TU_HASH_AQUI', 'admin'),
('cliente@test.com', '$2b$10$TU_HASH_AQUI', 'cliente');
```

## 🧪 Pruebas en Postman

### Paso 1: Login

1. **Endpoint**: `POST /api/login`
2. **Body** (raw JSON):
```json
{
  "correo": "admin@test.com",
  "contrasena": "password123"
}
```

**Nota**: El código acepta tanto `contrasena` (sin ñ) como `contraseña` (con ñ) para compatibilidad, pero se recomienda usar `contrasena`.
3. **Respuesta esperada**:
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Paso 2: Usar el token en rutas protegidas

1. Copia el `token` de la respuesta anterior
2. En cualquier request protegido, agrega en **Headers**:
   - **Key**: `Authorization`
   - **Value**: `Bearer <TU_TOKEN_AQUI>`

   Ejemplo: `Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...`

### Paso 3: Probar rutas protegidas

#### Rutas que requieren autenticación (cualquier rol):
- `GET /api/alumnos` - Obtener todos los alumnos
- `GET /api/talleres` - Obtener todos los talleres

#### Rutas que requieren rol ADMIN:
- `POST /api/alumnos` - Crear alumno
- `DELETE /api/alumnos/:id` - Eliminar alumno
- `POST /api/talleres` - Crear taller
- `DELETE /api/talleres/:id` - Eliminar taller

## 📝 Ejemplos de Respuestas

### Login exitoso (200 OK):
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Login con credenciales inválidas (401):
```json
{
  "error": "Credenciales inválidas"
}
```

### Sin token (401):
```json
{
  "error": "Token requerido. Inicia sesión primero."
}
```

### Token inválido (403):
```json
{
  "error": "Token inválido o expirado"
}
```

### Sin permisos de admin (403):
```json
{
  "error": "Acceso restringido solo para administradores"
}
```

## 🔒 Rutas Protegidas Implementadas

### Alumnos:
- ✅ `GET /api/alumnos` - Requiere token (cualquier rol)
- ✅ `POST /api/alumnos` - Requiere token + admin
- ✅ `DELETE /api/alumnos/:id` - Requiere token + admin

### Talleres:
- ✅ `GET /api/talleres` - Requiere token (cualquier rol)
- ✅ `POST /api/talleres` - Requiere token + admin
- ✅ `DELETE /api/talleres/:id` - Requiere token + admin

## 🛠️ Middlewares Disponibles

### `verificarToken`
Verifica que el token JWT sea válido. Agrega `req.usuario` con la información del usuario.

**Uso:**
```javascript
router.get('/ruta', verificarToken, (req, res) => {
  // req.usuario.id - ID del usuario
  // req.usuario.rol - Rol del usuario ('cliente' o 'admin')
});
```

### `soloAdmin`
Verifica que el usuario tenga rol 'admin'.

**Uso:**
```javascript
router.post('/ruta', verificarToken, soloAdmin, (req, res) => {
  // Solo admins pueden acceder aquí
});
```

### `soloCliente`
Verifica que el usuario tenga rol 'cliente'.

**Uso:**
```javascript
router.get('/ruta', verificarToken, soloCliente, (req, res) => {
  // Solo clientes pueden acceder aquí
});
```

## ✅ Checklist de Verificación

- [ ] Tabla `usuarios` creada en la base de datos
- [ ] Variable `JWT_SECRET` configurada en `.env`
- [ ] Usuario de prueba creado (admin y cliente)
- [ ] Login funciona correctamente
- [ ] Token se genera y se puede usar
- [ ] Rutas protegidas requieren token
- [ ] Rutas de admin solo permiten admins
- [ ] Errores de autenticación funcionan correctamente

## 🚀 Próximos Pasos

1. Proteger más rutas según tus necesidades
2. Agregar refresh tokens si es necesario
3. Implementar logout (invalidar tokens)
4. Agregar rate limiting para prevenir ataques de fuerza bruta

