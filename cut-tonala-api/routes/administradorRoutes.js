const express = require('express');
const router = express.Router();
const { body, validationResult } = require('express-validator');
const Administrador = require('../models/administradorModel');
const { uploadAdministrador } = require('../middlewares/uploadConfig');
const { verificarToken, soloAdmin } = require('../middlewares/authMiddleware');

// Validaciones
const validarAdministrador = [
  body('nombre').notEmpty().withMessage('El nombre es obligatorio').trim(),
  body('email').isEmail().withMessage('Debe proporcionar un email válido'),
  body('usuario').notEmpty().withMessage('El usuario es obligatorio').trim(),
  body('contrasena').isLength({ min: 6 }).withMessage('La contraseña debe tener al menos 6 caracteres'),
  body('telefono').optional().isLength({ min: 10, max: 20 }).withMessage('El teléfono debe tener entre 10 y 20 caracteres')
];

// GET: Obtener todos los administradores (SOLO ADMIN)
router.get('/administradores', verificarToken, soloAdmin, (req, res) => {
  Administrador.obtenerTodos((err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error al obtener administradores', detalle: err.message });
    }
    const administradoresConURL = resultados.map(admin => ({
      ...admin,
      foto_url: admin.foto ? `${process.env.BASE_URL}/uploads/administradores/${admin.foto}` : null
    }));
    res.status(200).json(administradoresConURL);
  });
});

// GET: Obtener administrador por ID (SOLO ADMIN)
router.get('/administradores/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Administrador.obtenerPorId(id, (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error interno del servidor', detalle: err.message });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún administrador con ese ID' });
    }
    
    const admin = resultados[0];
    admin.foto_url = admin.foto ? `${process.env.BASE_URL}/uploads/administradores/${admin.foto}` : null;
    res.status(200).json(admin);
  });
});

// POST: Crear nuevo administrador (SOLO ADMIN)
router.post('/administradores', verificarToken, soloAdmin, validarAdministrador, (req, res) => {
  const errores = validationResult(req);
  if (!errores.isEmpty()) {
    return res.status(400).json({ errores: errores.array() });
  }

  const { nombre, email, usuario, contrasena, telefono, estado } = req.body;

  // Verificar email único
  Administrador.buscarPorEmail(email, (err, resultado) => {
    if (err) {
      return res.status(500).json({ error: 'Error al verificar email', detalle: err.message });
    }
    if (resultado.length > 0) {
      return res.status(400).json({ error: 'El email ya está registrado' });
    }

    // Verificar usuario único
    Administrador.buscarPorUsuario(usuario, (err2, resultado2) => {
      if (err2) {
        return res.status(500).json({ error: 'Error al verificar usuario', detalle: err2.message });
      }
      if (resultado2.length > 0) {
        return res.status(400).json({ error: 'El usuario ya está registrado' });
      }

      const nuevoAdmin = { nombre, email, usuario, contrasena, telefono, estado };
      
      Administrador.crear(nuevoAdmin, (err3, resultado3) => {
        if (err3) {
          return res.status(500).json({ error: 'Error al crear administrador', detalle: err3.message });
        }
        res.status(201).json({
          mensaje: 'Administrador creado exitosamente',
          id: resultado3.insertId
        });
      });
    });
  });
});

// PUT: Actualizar administrador (SOLO ADMIN)
router.put('/administradores/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Administrador.obtenerPorId(id, (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error interno del servidor', detalle: err.message });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún administrador con ese ID' });
    }

    const datosActualizados = {};
    if (req.body.nombre) datosActualizados.nombre = req.body.nombre;
    if (req.body.email) datosActualizados.email = req.body.email;
    if (req.body.usuario) datosActualizados.usuario = req.body.usuario;
    if (req.body.contrasena) datosActualizados.contrasena = req.body.contrasena;
    if (req.body.telefono) datosActualizados.telefono = req.body.telefono;
    if (req.body.estado) datosActualizados.estado = req.body.estado;

    Administrador.actualizar(id, datosActualizados, (err2) => {
      if (err2) {
        return res.status(500).json({ error: 'Error al actualizar administrador', detalle: err2.message });
      }
      res.status(200).json({ mensaje: 'Administrador actualizado exitosamente' });
    });
  });
});

// DELETE: Eliminar administrador (SOLO ADMIN)
router.delete('/administradores/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Administrador.obtenerPorId(id, (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error interno del servidor', detalle: err.message });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún administrador con ese ID' });
    }

    Administrador.eliminar(id, (err2) => {
      if (err2) {
        return res.status(500).json({ error: 'Error al eliminar administrador', detalle: err2.message });
      }
      res.status(200).json({ mensaje: 'Administrador eliminado exitosamente' });
    });
  });
});

// POST: Subir foto de administrador (SOLO ADMIN)
router.post('/administradores/:id/foto', verificarToken, soloAdmin, uploadAdministrador.single('foto'), (req, res) => {
  const id = parseInt(req.params.id);

  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  if (!req.file) {
    return res.status(400).json({ error: 'No se ha proporcionado ninguna imagen' });
  }

  Administrador.obtenerPorId(id, (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error interno del servidor', detalle: err.message });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún administrador con ese ID' });
    }

    Administrador.actualizarFoto(id, req.file.filename, (err2) => {
      if (err2) {
        return res.status(500).json({ error: 'Error al guardar foto', detalle: err2.message });
      }
      res.status(200).json({
        mensaje: 'Foto actualizada exitosamente',
        foto: req.file.filename,
        foto_url: `${process.env.BASE_URL}/uploads/administradores/${req.file.filename}`
      });
    });
  });
});

module.exports = router;