const express = require('express');
const router = express.Router();
const { body, validationResult } = require('express-validator');
const Informacion = require('../models/informacionModel');
const { verificarToken, soloAdmin } = require('../middlewares/authMiddleware');

const validarInformacion = [
  body('tipo').isIn(['Aviso', 'Noticia', 'Evento']).withMessage('El tipo debe ser Aviso, Noticia o Evento'),
  body('contenido').notEmpty().withMessage('El contenido es obligatorio').trim(),
  body('admin_id').isInt({ min: 1 }).withMessage('El ID del administrador debe ser válido')
];

// GET: Obtener toda la información
router.get('/informacion', (req, res) => {
  Informacion.obtenerTodos((err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error al obtener información' });
    res.status(200).json(resultados);
  });
});

// GET: Obtener información por ID
router.get('/informacion/:id', (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Informacion.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró información con ese ID' });
    }
    res.status(200).json(resultados[0]);
  });
});

// GET: Obtener información por tipo
router.get('/informacion/tipo/:tipo', (req, res) => {
  const tipo = req.params.tipo;
  if (!['Aviso', 'Noticia', 'Evento'].includes(tipo)) {
    return res.status(400).json({ error: 'El tipo debe ser Aviso, Noticia o Evento' });
  }
  Informacion.obtenerPorTipo(tipo, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    res.status(200).json(resultados);
  });
});

// GET: Obtener información por administrador
router.get('/informacion/admin/:adminId', (req, res) => {
  const adminId = parseInt(req.params.adminId);
  if (!Number.isInteger(adminId) || adminId <= 0) {
    return res.status(400).json({ error: 'El ID del administrador no es válido' });
  }
  Informacion.obtenerPorAdmin(adminId, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    res.status(200).json(resultados);
  });
});

// POST: Crear nueva información (solo admin)
router.post('/informacion', verificarToken, soloAdmin, validarInformacion, (req, res) => {
  const errores = validationResult(req);
  if (!errores.isEmpty()) {
    return res.status(400).json({ errores: errores.array() });
  }

  const { tipo, contenido, admin_id } = req.body;
  Informacion.crear({ tipo, contenido, admin_id }, (err, resultado) => {
    if (err) return res.status(500).json({ error: 'Error al crear información' });
    res.status(201).json({ 
      mensaje: 'Información creada exitosamente', 
      id: resultado.insertId 
    });
  });
});

// PUT: Actualizar información (solo admin)
router.put('/informacion/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Informacion.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró información con ese ID' });
    }

    const datosActualizados = {};
    if (req.body.tipo) datosActualizados.tipo = req.body.tipo;
    if (req.body.contenido) datosActualizados.contenido = req.body.contenido;
    if (req.body.admin_id) datosActualizados.admin_id = req.body.admin_id;

    Informacion.actualizar(id, datosActualizados, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al actualizar información' });
      res.status(200).json({ mensaje: 'Información actualizada exitosamente' });
    });
  });
});

// DELETE: Eliminar información (solo admin)
router.delete('/informacion/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Informacion.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró información con ese ID' });
    }

    Informacion.eliminar(id, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al eliminar información' });
      res.status(200).json({ mensaje: 'Información eliminada exitosamente' });
    });
  });
});

module.exports = router;