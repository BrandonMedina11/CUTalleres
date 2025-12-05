// Script para verificar usuarios y probar login
const db = require('../config/db');
const bcrypt = require('bcrypt');
require('dotenv').config();

async function verificar() {
  console.log('🔍 ========== VERIFICACIÓN DE LOGIN ==========\n');

  // 1. Verificar usuarios existentes
  console.log('1️⃣ Usuarios en la base de datos:');
  db.query('SELECT id, correo, rol, nombre FROM usuarios', (err, usuarios) => {
    if (err) {
      console.error('❌ Error:', err.message);
      return;
    }

    if (usuarios.length === 0) {
      console.log('⚠️  No hay usuarios en la tabla\n');
    } else {
      console.log(`✅ Se encontraron ${usuarios.length} usuario(s):\n`);
      usuarios.forEach(usuario => {
        console.log(`   - ID: ${usuario.id}`);
        console.log(`     Correo: ${usuario.correo}`);
        console.log(`     Nombre: ${usuario.nombre || 'N/A'}`);
        console.log(`     Rol: ${usuario.rol}`);
        console.log('');
      });
    }

    // 2. Verificar usuario específico admin@test.com
    console.log('2️⃣ Verificando usuario admin@test.com...\n');
    db.query('SELECT * FROM usuarios WHERE correo = ?', ['admin@test.com'], async (err2, resultados) => {
      if (err2) {
        console.error('❌ Error:', err2.message);
        process.exit(1);
      }

      if (resultados.length === 0) {
        console.log('❌ El usuario admin@test.com NO existe');
        console.log('   Solución: Ejecuta: node scripts/crear_usuario.js admin@test.com password123 "Admin Test" admin\n');
        process.exit(1);
      }

      const usuario = resultados[0];
      console.log('✅ Usuario encontrado:');
      console.log(`   ID: ${usuario.id}`);
      console.log(`   Correo: ${usuario.correo}`);
      console.log(`   Nombre: ${usuario.nombre || 'N/A'}`);
      console.log(`   Rol: ${usuario.rol}`);
      console.log(`   Hash contraseña: ${usuario.contrasena ? usuario.contrasena.substring(0, 30) + '...' : 'NULL'}`);
      console.log('');

      // 3. Probar contraseña
      console.log('3️⃣ Probando contraseña "password123"...\n');
      if (!usuario.contrasena) {
        console.log('❌ El usuario no tiene contraseña configurada\n');
        process.exit(1);
      }

      const coincide = await bcrypt.compare('password123', usuario.contrasena);
      if (coincide) {
        console.log('✅ La contraseña "password123" es CORRECTA\n');
        console.log('✅ Todo está configurado correctamente!');
        console.log('   Puedes hacer login en Postman con:');
        console.log('   {');
        console.log('     "correo": "admin@test.com",');
        console.log('     "contrasena": "password123"');
        console.log('   }\n');
      } else {
        console.log('❌ La contraseña "password123" NO coincide');
        console.log('   El hash en la BD no corresponde a "password123"');
        console.log('   Solución: Elimina el usuario y créalo de nuevo\n');
      }

      process.exit(0);
    });
  });
}

verificar();

