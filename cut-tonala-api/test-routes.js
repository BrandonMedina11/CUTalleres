// Script para verificar que las rutas estén correctas
const authRoutes = require('./routes/authRoutes');
const express = require('express');
const app = express();

app.use('/api', authRoutes);

console.log('Rutas de autenticación cargadas:');
console.log('POST /api/login');
console.log('POST /api/registro');
console.log('GET /api/usuarios/perfil');

// Verificar que el controlador tenga el método
const authController = require('./controllers/authController');
console.log('\nMétodos del controlador:');
console.log('login:', typeof authController.login);
console.log('registro:', typeof authController.registro);
console.log('perfil:', typeof authController.perfil);


