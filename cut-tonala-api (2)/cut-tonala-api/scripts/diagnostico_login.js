// Script de diagnóstico para el problema de login
const db = require('../config/db');
const bcrypt = require('bcrypt');
require('dotenv').config();

async function diagnosticar() {
  console.log('🔍 ========== DIAGNÓSTICO DE LOGIN ==========\n');

  // 1. Verificar conexión a la base de datos
  console.log('1️⃣ Verificando conexión a la base de datos...');
  try {
    await new Promise((resolve, reject) => {
      db.query('SELECT 1', (err) => {
        if (err) reject(err);
        else resolve();
      });
    });
    console.log('✅ Conexión a la base de datos: OK\n');
  } catch (err) {
    console.error('❌ Error de conexión:', err.message);
    console.log('\n');
  }

  // 2. Verificar si existe la tabla usuarios
  console.log('2️⃣ Verificando si existe la tabla "usuarios"...');
  try {
    const resultado = await new Promise((resolve, reject) => {
      db.query("SHOW TABLES LIKE 'usuarios'", (err, results) => {
        if (err) reject(err);
        else resolve(results);
      });
    });
    
    if (resultado.length > 0) {
      console.log('✅ La tabla "usuarios" existe\n');
    } else {
      console.log('❌ La tabla "usuarios" NO existe');
      console.log('   Solución: Ejecuta database/usuarios_table.sql\n');
    }
  } catch (err) {
    console.error('❌ Error al verificar tabla:', err.message, '\n');
  }

  // 3. Verificar estructura de la tabla usuarios
  console.log('3️⃣ Verificando estructura de la tabla "usuarios"...');
  try {
    const estructura = await new Promise((resolve, reject) => {
      db.query('DESCRIBE usuarios', (err, results) => {
        if (err) reject(err);
        else resolve(results);
      });
    });
    
    console.log('✅ Estructura de la tabla:');
    estructura.forEach(col => {
      console.log(`   - ${col.Field} (${col.Type})`);
    });
    console.log('');
  } catch (err) {
    console.error('❌ Error al obtener estructura:', err.message);
    console.log('   (Esto es normal si la tabla no existe)\n');
  }

  // 4. Verificar usuarios existentes
  console.log('4️⃣ Verificando usuarios en la base de datos...');
  try {
    const usuarios = await new Promise((resolve, reject) => {
      db.query('SELECT id, correo, rol, contraseña FROM usuarios', (err, results) => {
        if (err) reject(err);
        else resolve(results);
      });
    });
    
    if (usuarios.length === 0) {
      console.log('⚠️  No hay usuarios en la tabla');
      console.log('   Solución: Crea usuarios con: node scripts/crear_usuario.js\n');
    } else {
      console.log(`✅ Se encontraron ${usuarios.length} usuario(s):`);
      usuarios.forEach(usuario => {
        console.log(`   - ID: ${usuario.id}, Correo: ${usuario.correo}, Rol: ${usuario.rol}`);
        console.log(`     Hash contraseña: ${usuario.contraseña ? usuario.contraseña.substring(0, 20) + '...' : 'NULL'}`);
      });
      console.log('');
    }
  } catch (err) {
    console.error('❌ Error al obtener usuarios:', err.message);
    console.log('   (Esto puede ser normal si la tabla no existe o tiene otro nombre)\n');
  }

  // 5. Verificar usuario específico admin@test.com
  console.log('5️⃣ Verificando usuario admin@test.com...');
  try {
    const usuario = await new Promise((resolve, reject) => {
      db.query('SELECT * FROM usuarios WHERE correo = ?', ['admin@test.com'], (err, results) => {
        if (err) reject(err);
        else resolve(results);
      });
    });
    
    if (usuario.length === 0) {
      console.log('❌ El usuario admin@test.com NO existe');
      console.log('   Solución: Crea el usuario con:');
      console.log('   node scripts/crear_usuario.js admin@test.com password123 admin\n');
    } else {
      console.log('✅ Usuario encontrado:');
      const u = usuario[0];
      console.log(`   - ID: ${u.id}`);
      console.log(`   - Correo: ${u.correo}`);
      console.log(`   - Rol: ${u.rol}`);
      console.log(`   - Hash existe: ${u.contraseña ? 'Sí' : 'No'}`);
      
      // Probar contraseña
      if (u.contraseña) {
        const coincide = await bcrypt.compare('password123', u.contraseña);
        console.log(`   - Prueba de contraseña "password123": ${coincide ? '✅ Correcta' : '❌ Incorrecta'}`);
      }
      console.log('');
    }
  } catch (err) {
    console.error('❌ Error al verificar usuario:', err.message, '\n');
  }

  // 6. Verificar JWT_SECRET
  console.log('6️⃣ Verificando variable JWT_SECRET...');
  if (process.env.JWT_SECRET) {
    console.log('✅ JWT_SECRET está configurado');
    console.log(`   Valor: ${process.env.JWT_SECRET.substring(0, 10)}...\n`);
  } else {
    console.log('❌ JWT_SECRET NO está configurado');
    console.log('   Solución: Agrega JWT_SECRET=mitokenseguro123 en tu archivo .env\n');
  }

  // 7. Verificar si existe tabla administradores (por si acaso)
  console.log('7️⃣ Verificando si existe tabla "administradores"...');
  try {
    const resultado = await new Promise((resolve, reject) => {
      db.query("SHOW TABLES LIKE 'administradores'", (err, results) => {
        if (err) reject(err);
        else resolve(results);
      });
    });
    
    if (resultado.length > 0) {
      console.log('⚠️  La tabla "administradores" existe');
      console.log('   Nota: El login ahora usa la tabla "usuarios", no "administradores"\n');
    } else {
      console.log('✅ No hay tabla "administradores" (esto está bien)\n');
    }
  } catch (err) {
    console.log('   (No se pudo verificar)\n');
  }

  console.log('========== FIN DEL DIAGNÓSTICO ==========');
  console.log('\n💡 Si hay errores, sigue las soluciones sugeridas arriba.');
  
  process.exit(0);
}

diagnosticar().catch(err => {
  console.error('❌ Error fatal:', err);
  process.exit(1);
});

