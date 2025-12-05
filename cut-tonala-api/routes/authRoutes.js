const express = require('express');
const router = express.Router();
const authController = require('../controllers/authController');
const { verificarToken } = require('../middlewares/authMiddleware');

// Rutas públicas de autenticación
router.post('/login', authController.login);
router.post('/registro', authController.registro);

// Ruta protegida - Perfil del usuario autenticado
router.get('/usuarios/perfil', verificarToken, authController.perfil);

module.exports = router;
