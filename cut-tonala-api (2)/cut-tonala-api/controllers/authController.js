const db = require('../config/db');
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');

// LOGIN - Para usuarios (administradores y clientes)
exports.login = async (req, res) => {
  console.log('========== LOGIN DEBUG ==========');
  console.log('Body completo:', req.body);
  console.log('Correo:', req.body.correo);
  console.log('Contrasena:', req.body.contrasena || req.body.contraseña);
  console.log('================================');

  // Aceptar tanto "contrasena" como "contraseña" para compatibilidad
  const correo = req.body.correo;
  const contrasena = req.body.contrasena || req.body.contraseña;

  // Validar que vengan los datos
  if (!correo || !contrasena) {
    console.log('❌ Faltan datos');
    return res.status(400).json({ error: 'Se requieren correo y contraseña' });
  }

  // Buscar usuario por correo en la tabla usuarios
  db.query('SELECT * FROM usuarios WHERE correo = ?', [correo], async (err, resultados) => {
    if (err) {
      console.error('❌ Error en la base de datos:', err);
      return res.status(500).json({ error: 'Error en la base de datos' });
    }

    console.log('📊 Resultados encontrados:', resultados.length);

    if (resultados.length === 0) {
      console.log('❌ No existe el correo:', correo);
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }

    const usuario = resultados[0];
    console.log('✅ Usuario encontrado:', usuario.correo);
    // La BD usa 'contrasena' (sin ñ) según la estructura real
    const hashContrasena = usuario.contrasena;
    console.log('🔐 Hash en BD:', hashContrasena);
    console.log('🔑 Contraseña recibida:', contrasena);

    // Comparar contraseñas (acepta ambos nombres de campo)
    const coincide = await bcrypt.compare(contrasena, hashContrasena);
    console.log('🔍 ¿Contraseña coincide?:', coincide);
    
    if (!coincide) {
      console.log('❌ Contraseña incorrecta');
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }

    console.log('✅ Login exitoso! Generando token...');

    // Generar token JWT
    const token = jwt.sign(
      { 
        id: usuario.id, 
        rol: usuario.rol,
        email: usuario.correo,
        correo: usuario.correo
      }, 
      process.env.JWT_SECRET, 
      { expiresIn: '1h' }
    );

    console.log('✅ Token generado exitosamente');

    res.json({ 
      token,
      usuario: {
        id: usuario.id,
        correo: usuario.correo,
        rol: usuario.rol
      }
    });
  });
};

// REGISTRO (para crear nuevos usuarios con contraseña cifrada)
exports.registro = async (req, res) => {
  const { correo, contrasena, rol, nombre } = req.body; // SIN ñ

  // Validaciones
  if (!correo || !contrasena || !nombre) {
    return res.status(400).json({ error: 'Correo, contraseña y nombre son obligatorios' });
  }

  if (contrasena.length < 6) {
    return res.status(400).json({ error: 'La contraseña debe tener al menos 6 caracteres' });
  }

  // Verificar si el correo ya existe
  db.query('SELECT * FROM usuarios WHERE correo = ?', [correo], async (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error en la base de datos' });
    }

    if (resultados.length > 0) {
      return res.status(400).json({ error: 'El correo ya está registrado' });
    }

    // Cifrar contraseña
    const contrasenaCifrada = await bcrypt.hash(contrasena, 10);

    const nuevoUsuario = {
      correo,
      contrasena: contrasenaCifrada, // Sin ñ según la estructura real de la BD
      rol: rol || 'alumno',
      nombre
    };

    // Insertar usuario en la base de datos
    db.query('INSERT INTO usuarios SET ?', nuevoUsuario, (err2, resultado) => {
      if (err2) {
        return res.status(500).json({ error: 'Error al crear usuario' });
      }

      res.status(201).json({ 
        mensaje: 'Usuario registrado exitosamente',
        id: resultado.insertId
      });
    });
  });
};

// Perfil del usuario autenticado
exports.perfil = (req, res) => {
  // req.usuario viene del middleware de autenticación
  db.query('SELECT id, correo, nombre, rol FROM usuarios WHERE id = ?', [req.usuario.id], (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error al obtener perfil' });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'Usuario no encontrado' });
    }
    res.json(resultados[0]);
  });
};