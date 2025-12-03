const express = require('express');
const router = express.Router();
const { body, validationResult } = require('express-validator');
const Taller = require('../models/tallerModel');
const { uploadTaller } = require('../middlewares/uploadConfig');
const { verificarToken, soloAdmin } = require('../middlewares/authMiddleware');
const db = require('../config/db');

const validarTaller = [
  body('nombre').notEmpty().withMessage('El nombre es obligatorio').trim(),
  body('descripcion').optional().trim(),
  body('categoria_id').isInt({ min: 1 }).withMessage('El ID de categoría debe ser un número válido'),
  body('profesor_id').isInt({ min: 1 }).withMessage('El ID de profesor debe ser un número válido')
];

// GET: Obtener todos los talleres (requiere autenticación)
router.get('/talleres', verificarToken, (req, res) => {
  Taller.obtenerTodos((err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error al obtener talleres' });
    const talleresConURL = resultados.map(t => ({
      ...t,
      foto_url: t.foto ? `${process.env.BASE_URL}/uploads/talleres/${t.foto}` : null
    }));
    res.status(200).json(talleresConURL);
  });
});

// GET: Obtener taller por ID (requiere autenticación)
router.get('/talleres/:id', verificarToken, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Taller.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún taller con ese ID' });
    }
    const taller = resultados[0];
    taller.foto_url = taller.foto ? `${process.env.BASE_URL}/uploads/talleres/${taller.foto}` : null;
    res.status(200).json(taller);
  });
});

// GET: Obtener talleres por categoría (requiere autenticación)
router.get('/talleres/categoria/:categoriaId', verificarToken, (req, res) => {
  const categoriaId = parseInt(req.params.categoriaId);
  if (!Number.isInteger(categoriaId) || categoriaId <= 0) {
    return res.status(400).json({ error: 'El ID de categoría no es válido' });
  }
  Taller.obtenerPorCategoria(categoriaId, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    res.status(200).json(resultados);
  });
});

// GET: Obtener talleres por profesor (requiere autenticación)
router.get('/talleres/profesor/:profesorId', verificarToken, (req, res) => {
  const profesorId = parseInt(req.params.profesorId);
  if (!Number.isInteger(profesorId) || profesorId <= 0) {
    return res.status(400).json({ error: 'El ID de profesor no es válido' });
  }
  Taller.obtenerPorProfesor(profesorId, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    res.status(200).json(resultados);
  });
});

// GET: Obtener taller con información de inscritos (requiere autenticación)
router.get('/talleres/:id/inscritos', verificarToken, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Taller.obtenerConInscritos(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún taller con ese ID' });
    }
    res.status(200).json(resultados[0]);
  });
});

// POST: Crear nuevo taller (solo admin)
router.post('/talleres', verificarToken, soloAdmin, validarTaller, (req, res) => {
  const errores = validationResult(req);
  if (!errores.isEmpty()) {
    return res.status(400).json({ errores: errores.array() });
  }
  const { nombre, descripcion, categoria_id, profesor_id } = req.body;
  Taller.crear({ nombre, descripcion, categoria_id, profesor_id }, (err, resultado) => {
    if (err) return res.status(500).json({ error: 'Error al crear taller' });
    res.status(201).json({ mensaje: 'Taller creado exitosamente', id: resultado.insertId });
  });
});

// PUT: Actualizar taller (solo admin)
router.put('/talleres/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Taller.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún taller con ese ID' });
    }
    const datosActualizados = {};
    if (req.body.nombre) datosActualizados.nombre = req.body.nombre;
    if (req.body.descripcion) datosActualizados.descripcion = req.body.descripcion;
    if (req.body.categoria_id) datosActualizados.categoria_id = req.body.categoria_id;
    if (req.body.profesor_id) datosActualizados.profesor_id = req.body.profesor_id;
    Taller.actualizar(id, datosActualizados, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al actualizar taller' });
      res.status(200).json({ mensaje: 'Taller actualizado exitosamente' });
    });
  });
});

// DELETE: Eliminar taller (solo admin)
router.delete('/talleres/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Taller.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún taller con ese ID' });
    }

    // Primero: Eliminar todas las inscripciones del taller (si existen)
    // Esto debe hacerse ANTES de eliminar el taller para evitar errores de clave foránea
    db.query('DELETE FROM inscripciones WHERE taller_id = ?', [id], (errInscripciones, resultInscripciones) => {
      if (errInscripciones) {
        console.error('Error al eliminar inscripciones:', errInscripciones);
        return res.status(500).json({ 
          error: 'Error al eliminar inscripciones del taller', 
          detalle: errInscripciones.message 
        });
      }

      console.log(`Inscripciones eliminadas: ${resultInscripciones ? resultInscripciones.affectedRows : 0}`);

      // Segundo: Ahora sí podemos eliminar el taller de forma segura
      Taller.eliminar(id, (errTaller) => {
        if (errTaller) {
          // Verificar si es un error de clave foránea
          if (errTaller.code === 'ER_ROW_IS_REFERENCED_2' || errTaller.code === '23000' || errTaller.errno === 1451) {
            return res.status(400).json({ 
              error: 'No se puede eliminar el taller porque aún tiene inscripciones asociadas. Intenta eliminar las inscripciones manualmente primero.',
              detalle: errTaller.message
            });
          }
          return res.status(500).json({ 
            error: 'Error al eliminar taller', 
            detalle: errTaller.message 
          });
        }

        // Éxito: ambas operaciones completadas
        const inscripcionesEliminadas = resultInscripciones ? resultInscripciones.affectedRows : 0;
        res.status(200).json({ 
          mensaje: 'Taller eliminado exitosamente',
          inscripciones_eliminadas: inscripcionesEliminadas
        });
      });
    });
  });
});

// POST: Subir foto de taller (solo admin)
router.post('/talleres/:id/foto', verificarToken, soloAdmin, uploadTaller.single('foto'), (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  if (!req.file) {
    return res.status(400).json({ error: 'No se ha proporcionado ninguna imagen' });
  }
  Taller.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún taller con ese ID' });
    }
    Taller.actualizarFoto(id, req.file.filename, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al guardar foto' });
      res.status(200).json({
        mensaje: 'Foto actualizada exitosamente',
        foto: req.file.filename,
        foto_url: `${process.env.BASE_URL}/uploads/talleres/${req.file.filename}`
      });
    });
  });
});

module.exports = router;