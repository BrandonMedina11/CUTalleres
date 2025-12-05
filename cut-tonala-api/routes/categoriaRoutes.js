const express = require('express');
const router = express.Router();
const { body, validationResult } = require('express-validator');
const Categoria = require('../models/categoriaModel');
const { uploadCategoria } = require('../middlewares/uploadConfig');
const { verificarToken, soloAdmin } = require('../middlewares/authMiddleware');

const validarCategoria = [
  body('nombre').notEmpty().withMessage('El nombre es obligatorio').trim(),
  body('descripcion').optional().trim()
];

router.get('/categorias', (req, res) => {
  Categoria.obtenerTodos((err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error al obtener categorías' });
    const categoriasConURL = resultados.map(c => ({
      ...c,
      foto_url: c.foto ? `${process.env.BASE_URL}/uploads/categorias/${c.foto}` : null
    }));
    res.status(200).json(categoriasConURL);
  });
});

router.get('/categorias/:id', (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Categoria.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ninguna categoría con ese ID' });
    }
    const categoria = resultados[0];
    categoria.foto_url = categoria.foto ? `${process.env.BASE_URL}/uploads/categorias/${categoria.foto}` : null;
    res.status(200).json(categoria);
  });
});

router.get('/categorias/:id/talleres', (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Categoria.obtenerConTalleres(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ninguna categoría con ese ID' });
    }
    res.status(200).json(resultados);
  });
});

router.post('/categorias', verificarToken, soloAdmin, validarCategoria, (req, res) => {
  const errores = validationResult(req);
  if (!errores.isEmpty()) {
    return res.status(400).json({ errores: errores.array() });
  }
  const { nombre, descripcion } = req.body;
  Categoria.crear({ nombre, descripcion }, (err, resultado) => {
    if (err) return res.status(500).json({ error: 'Error al crear categoría' });
    res.status(201).json({ mensaje: 'Categoría creada exitosamente', id: resultado.insertId });
  });
});

router.put('/categorias/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Categoria.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ninguna categoría con ese ID' });
    }
    const datosActualizados = {};
    if (req.body.nombre) datosActualizados.nombre = req.body.nombre;
    if (req.body.descripcion) datosActualizados.descripcion = req.body.descripcion;
    Categoria.actualizar(id, datosActualizados, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al actualizar categoría' });
      res.status(200).json({ mensaje: 'Categoría actualizada exitosamente' });
    });
  });
});

router.delete('/categorias/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Categoria.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ninguna categoría con ese ID' });
    }
    Categoria.eliminar(id, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al eliminar categoría' });
      res.status(200).json({ mensaje: 'Categoría eliminada exitosamente' });
    });
  });
});

router.post('/categorias/:id/foto', verificarToken, soloAdmin, uploadCategoria.single('foto'), (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  if (!req.file) {
    return res.status(400).json({ error: 'No se ha proporcionado ninguna imagen' });
  }
  Categoria.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ninguna categoría con ese ID' });
    }
    Categoria.actualizarFoto(id, req.file.filename, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al guardar foto' });
      res.status(200).json({
        mensaje: 'Foto actualizada exitosamente',
        foto: req.file.filename,
        foto_url: `${process.env.BASE_URL}/uploads/categorias/${req.file.filename}`
      });
    });
  });
});

module.exports = router;