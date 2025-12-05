const express = require('express');
const cors = require('cors');
const path = require('path');
require('dotenv').config();

const app = express();

// Middlewares
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Servir archivos estáticos (imágenes)
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// Importar rutas
const authRoutes = require('./routes/authRoutes');
const administradorRoutes = require('./routes/administradorRoutes');
const alumnoRoutes = require('./routes/alumnoRoutes');
const profesorRoutes = require('./routes/profesorRoutes');
const categoriaRoutes = require('./routes/categoriaRoutes');
const tallerRoutes = require('./routes/tallerRoutes');
const inscripcionRoutes = require('./routes/inscripcionRoutes');
const informacionRoutes = require('./routes/informacionRoutes');
const productoRoutes = require('./routes/productoRoutes');

// Usar rutas
app.use('/api', authRoutes);  // ← NUEVA: Rutas de autenticación
app.use('/api', administradorRoutes);
app.use('/api', alumnoRoutes);
app.use('/api', profesorRoutes);
app.use('/api', categoriaRoutes);
app.use('/api', tallerRoutes);
app.use('/api', inscripcionRoutes);
app.use('/api', informacionRoutes);
app.use('/api', productoRoutes);

// Ruta de prueba
app.get('/', (req, res) => {
  res.json({ 
    mensaje: 'API CUT Tonalá funcionando correctamente',
    version: '2.0.0',
    autenticacion: 'JWT implementado'
  });
});

// Manejo de errores
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({ 
    error: 'Algo salió mal en el servidor',
    detalle: err.message 
  });
});

// Iniciar servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`========================================`);
  console.log(` Servidor corriendo en puerto ${PORT}`);
  console.log(` http://localhost:${PORT}`);
  console.log(` API disponible en http://localhost:${PORT}/api`);
  console.log(` Autenticación JWT activada`);
  console.log(`========================================`);
});