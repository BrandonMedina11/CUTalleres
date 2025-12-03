const express = require('express');
const router = express.Router();
const { body, validationResult } = require('express-validator');
const Producto = require('../models/productoModel');
const { uploadProducto } = require('../middlewares/uploadConfig');
const db = require('../config/db');

const validarProducto = [
  body('nombre').notEmpty().withMessage('El nombre es obligatorio').trim(),
  body('descripcion').optional().trim(),
  body('marca').notEmpty().withMessage('La marca es obligatoria').trim(),
  body('precio').isFloat({ min: 0 }).withMessage('El precio debe ser un número positivo'),
  body('existencia').isInt({ min: 0 }).withMessage('La existencia debe ser un número entero positivo')
];

// GET: Obtener todos los productos (público - sin autenticación para el cliente)
router.get('/productos', (req, res) => {
  Producto.obtenerTodos((err, resultados) => {
    if (err) {
      console.error('Error al obtener productos:', err);
      return res.status(500).json({ error: 'Error al obtener productos' });
    }
    const productosConURL = resultados.map(p => ({
      ...p,
      imagen1_url: p.imagen1 ? `${process.env.BASE_URL || 'http://localhost:3000'}/uploads/productos/${p.imagen1}` : null,
      imagen2_url: p.imagen2 ? `${process.env.BASE_URL || 'http://localhost:3000'}/uploads/productos/${p.imagen2}` : null,
      imagen3_url: p.imagen3 ? `${process.env.BASE_URL || 'http://localhost:3000'}/uploads/productos/${p.imagen3}` : null
    }));
    res.status(200).json(productosConURL);
  });
});

// GET: Obtener producto por ID (público - sin autenticación para el cliente)
router.get('/productos/:id', (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Producto.obtenerPorId(id, (err, resultados) => {
    if (err) {
      console.error('Error al obtener producto:', err);
      return res.status(500).json({ error: 'Error interno del servidor' });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún producto con ese ID' });
    }
    const producto = resultados[0];
    producto.imagen1_url = producto.imagen1 ? `${process.env.BASE_URL || 'http://localhost:3000'}/uploads/productos/${producto.imagen1}` : null;
    producto.imagen2_url = producto.imagen2 ? `${process.env.BASE_URL || 'http://localhost:3000'}/uploads/productos/${producto.imagen2}` : null;
    producto.imagen3_url = producto.imagen3 ? `${process.env.BASE_URL || 'http://localhost:3000'}/uploads/productos/${producto.imagen3}` : null;
    res.status(200).json(producto);
  });
});

module.exports = router;




