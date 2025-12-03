const express = require('express');
const router = express.Router();
const { body, validationResult } = require('express-validator');
const Inscripcion = require('../models/inscripcionModel');
const { verificarToken, soloAdmin } = require('../middlewares/authMiddleware');

const validarInscripcion = [
  body('alumno_id').isInt({ min: 1 }).withMessage('El ID del alumno debe ser válido'),
  body('taller_id').isInt({ min: 1 }).withMessage('El ID del taller debe ser válido'),
  body('fecha_registro').isDate().withMessage('La fecha debe tener formato válido'),
  body('estado').optional().isIn(['activo', 'inactivo']).withMessage('El estado debe ser activo o inactivo')
];

// GET: Obtener todas las inscripciones (requiere autenticación)
router.get('/inscripciones', verificarToken, (req, res) => {
  Inscripcion.obtenerTodos((err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error al obtener inscripciones' });
    res.status(200).json(resultados);
  });
});

// GET: Obtener inscripción por ID
router.get('/inscripciones/:id', (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Inscripcion.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ninguna inscripción con ese ID' });
    }
    res.status(200).json(resultados[0]);
  });
});

// GET: Obtener inscripciones por alumno
router.get('/inscripciones/alumno/:alumnoId', (req, res) => {
  const alumnoId = parseInt(req.params.alumnoId);
  if (!Number.isInteger(alumnoId) || alumnoId <= 0) {
    return res.status(400).json({ error: 'El ID del alumno no es válido' });
  }
  Inscripcion.obtenerPorAlumno(alumnoId, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    res.status(200).json(resultados);
  });
});

// GET: Obtener inscripciones por taller
router.get('/inscripciones/taller/:tallerId', (req, res) => {
  const tallerId = parseInt(req.params.tallerId);
  if (!Number.isInteger(tallerId) || tallerId <= 0) {
    return res.status(400).json({ error: 'El ID del taller no es válido' });
  }
  Inscripcion.obtenerPorTaller(tallerId, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    res.status(200).json(resultados);
  });
});

// POST: Crear nueva inscripción (requiere autenticación)
router.post('/inscripciones', verificarToken, validarInscripcion, (req, res) => {
  const errores = validationResult(req);
  if (!errores.isEmpty()) {
    return res.status(400).json({ errores: errores.array() });
  }

  const { alumno_id, taller_id, fecha_registro, estado } = req.body;

  // Verificar si ya existe inscripción activa
  Inscripcion.verificarInscripcionExistente(alumno_id, taller_id, (err, resultado) => {
    if (err) return res.status(500).json({ error: 'Error al verificar inscripción' });
    if (resultado.length > 0) {
      return res.status(400).json({ error: 'El alumno ya está inscrito en este taller' });
    }

    const nuevaInscripcion = { 
      alumno_id, 
      taller_id, 
      fecha_registro, 
      estado: estado || 'activo' 
    };

    Inscripcion.crear(nuevaInscripcion, (err2, resultado2) => {
      if (err2) return res.status(500).json({ error: 'Error al crear inscripción' });
      res.status(201).json({ 
        mensaje: 'Inscripción creada exitosamente', 
        id: resultado2.insertId 
      });
    });
  });
});

// PUT: Actualizar inscripción (solo admin)
router.put('/inscripciones/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Inscripcion.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ninguna inscripción con ese ID' });
    }

    const datosActualizados = {};
    if (req.body.fecha_registro) datosActualizados.fecha_registro = req.body.fecha_registro;
    if (req.body.estado) datosActualizados.estado = req.body.estado;

    Inscripcion.actualizar(id, datosActualizados, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al actualizar inscripción' });
      res.status(200).json({ mensaje: 'Inscripción actualizada exitosamente' });
    });
  });
});

// DELETE: Eliminar inscripción (solo admin)
router.delete('/inscripciones/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Inscripcion.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ninguna inscripción con ese ID' });
    }

    Inscripcion.eliminar(id, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al eliminar inscripción' });
      res.status(200).json({ mensaje: 'Inscripción eliminada exitosamente' });
    });
  });
});

module.exports = router;