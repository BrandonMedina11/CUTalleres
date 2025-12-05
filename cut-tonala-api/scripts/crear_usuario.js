// Script para crear usuarios con contraseña cifrada
// Uso: node scripts/crear_usuario.js

const bcrypt = require('bcrypt');
const db = require('../config/db');
require('dotenv').config();

async function crearUsuario(correo, contrasena, nombre, rol = 'alumno') {
  try {
    // Cifrar contraseña
    const contrasenaCifrada = await bcrypt.hash(contrasena, 10);
    
    // Insertar usuario (la BD usa 'contrasena' sin ñ y requiere 'nombre')
    db.query(
      'INSERT INTO usuarios (correo, contrasena, nombre, rol) VALUES (?, ?, ?, ?)',
      [correo, contrasenaCifrada, nombre, rol],
      (err, resultado) => {
        if (err) {
          console.error('❌ Error al crear usuario:', err.message);
          return;
        }
        console.log('✅ Usuario creado exitosamente');
        console.log(`   ID: ${resultado.insertId}`);
        console.log(`   Correo: ${correo}`);
        console.log(`   Nombre: ${nombre}`);
        console.log(`   Rol: ${rol}`);
        process.exit(0);
      }
    );
  } catch (error) {
    console.error('❌ Error:', error.message);
    process.exit(1);
  }
}

// Obtener argumentos de la línea de comandos
const args = process.argv.slice(2);
if (args.length < 3) {
  console.log('Uso: node scripts/crear_usuario.js <correo> <contrasena> <nombre> [rol]');
  console.log('Ejemplo: node scripts/crear_usuario.js admin@test.com password123 "Admin Principal" admin');
  console.log('Roles disponibles: alumno, admin, profesor');
  process.exit(1);
}

const [correo, contrasena, nombre, rol] = args;
crearUsuario(correo, contrasena, nombre, rol || 'alumno');

