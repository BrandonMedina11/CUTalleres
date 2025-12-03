const bcrypt = require('bcrypt');
const mysql = require('mysql2');
require('dotenv').config();

// Usar las variables de entorno
const db = mysql.createConnection({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_NAME
});

async function crearUsuario() {
  try {
    // Conectar a la base de datos
    db.connect((err) => {
      if (err) {
        console.error('❌ Error conectando a la BD:');
        console.error('Código:', err.code);
        console.error('Mensaje:', err.message);
        console.error('SQL State:', err.sqlState);
        console.error('\n📋 Verifica tu configuración:');
        console.error('Host:', process.env.DB_HOST);
        console.error('User:', process.env.DB_USER);
        console.error('Database:', process.env.DB_NAME);
        process.exit(1);
      }
      console.log('✅ Conectado a la base de datos:', process.env.DB_NAME);
      insertarUsuario();
    });

  } catch (error) {
    console.error('❌ Error:', error);
    process.exit(1);
  }
}

async function insertarUsuario() {
  // Datos del usuario
  const correo = 'admin@test.com';
  const contrasena = 'password123';
  const nombre = 'Administrador';
  const rol = 'admin';

  // Hashear la contraseña
  console.log('Hasheando contraseña...');
  const contrasenaHash = await bcrypt.hash(contrasena, 10);
  console.log('Contraseña hasheada ✓');

  // Insertar en la base de datos
  const query = 'INSERT INTO usuarios (correo, contraseña, nombre, rol) VALUES (?, ?, ?, ?)';
  
  db.query(query, [correo, contrasenaHash, nombre, rol], (err, resultado) => {
    if (err) {
      console.error('❌ Error al crear usuario:');
      console.error('Código:', err.code);
      console.error('Mensaje:', err.message);
      db.end();
      process.exit(1);
    }

    console.log('\n✅ Usuario creado exitosamente!');
    console.log('================================');
    console.log('📧 Correo:', correo);
    console.log('🔑 Contraseña:', contrasena);
    console.log('👤 Nombre:', nombre);
    console.log('🎭 Rol:', rol);
    console.log('🆔 ID:', resultado.insertId);
    console.log('================================\n');
    
    db.end();
    process.exit(0);
  });
}

crearUsuario();