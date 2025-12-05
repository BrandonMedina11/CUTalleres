const express = require('express');
const router = express.Router();
const { body, validationResult } = require('express-validator');
const Alumno = require('../models/alumnoModel');
const { uploadAlumno } = require('../middlewares/uploadConfig');
const { verificarToken, soloAdmin } = require('../middlewares/authMiddleware');
const db = require('../config/db');

// Validaciones
const validarAlumno = [
  body('nombre').notEmpty().withMessage('El nombre es obligatorio').trim(),
  body('email').isEmail().withMessage('Debe proporcionar un email válido'),
  body('codigo').notEmpty().withMessage('El código es obligatorio').trim(),
  body('carrera').optional().trim()
];

// GET: Obtener todos los alumnos (requiere autenticación)
router.get('/alumnos', verificarToken, (req, res) => {
  Alumno.obtenerTodos((err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error al obtener alumnos', detalle: err.message });
    }
    const alumnosConURL = resultados.map(alumno => ({
      ...alumno,
      foto_url: alumno.foto ? `${process.env.BASE_URL}/uploads/alumnos/${alumno.foto}` : null
    }));
    res.status(200).json(alumnosConURL);
  });
});

// GET: Obtener alumno por ID
router.get('/alumnos/:id', (req, res) => {
  const id = parseInt(req.params.id);
  
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Alumno.obtenerPorId(id, (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error interno del servidor', detalle: err.message });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún alumno con ese ID' });
    }
    
    const alumno = resultados[0];
    alumno.foto_url = alumno.foto ? `${process.env.BASE_URL}/uploads/alumnos/${alumno.foto}` : null;
    res.status(200).json(alumno);
  });
});

// GET: Obtener alumno con sus talleres inscritos
router.get('/alumnos/:id/talleres', (req, res) => {
  const id = parseInt(req.params.id);
  
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Alumno.obtenerConTalleres(id, (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error interno del servidor', detalle: err.message });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún alumno con ese ID' });
    }
    res.status(200).json(resultados);
  });
});

// POST: Crear nuevo alumno (solo admin)
router.post('/alumnos', verificarToken, soloAdmin, validarAlumno, (req, res) => {
  const errores = validationResult(req);
  if (!errores.isEmpty()) {
    return res.status(400).json({ errores: errores.array() });
  }

  const { nombre, email, codigo, carrera } = req.body;

  // Verificar email único
  Alumno.buscarPorEmail(email, (err, resultado) => {
    if (err) {
      return res.status(500).json({ error: 'Error al verificar email', detalle: err.message });
    }
    if (resultado.length > 0) {
      return res.status(400).json({ error: 'El email ya está registrado' });
    }

    // Verificar código único
    Alumno.buscarPorCodigo(codigo, (err2, resultado2) => {
      if (err2) {
        return res.status(500).json({ error: 'Error al verificar código', detalle: err2.message });
      }
      if (resultado2.length > 0) {
        return res.status(400).json({ error: 'El código ya está registrado' });
      }

      const nuevoAlumno = { nombre, email, codigo, carrera };
      
      Alumno.crear(nuevoAlumno, (err3, resultado3) => {
        if (err3) {
          return res.status(500).json({ error: 'Error al crear alumno', detalle: err3.message });
        }
        res.status(201).json({
          mensaje: 'Alumno creado exitosamente',
          id: resultado3.insertId
        });
      });
    });
  });
});

// PUT: Actualizar alumno (solo admin)
router.put('/alumnos/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Alumno.obtenerPorId(id, (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error interno del servidor', detalle: err.message });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún alumno con ese ID' });
    }

    const datosActualizados = {};
    if (req.body.nombre) datosActualizados.nombre = req.body.nombre;
    if (req.body.email) datosActualizados.email = req.body.email;
    if (req.body.codigo) datosActualizados.codigo = req.body.codigo;
    if (req.body.carrera) datosActualizados.carrera = req.body.carrera;

    Alumno.actualizar(id, datosActualizados, (err2) => {
      if (err2) {
        return res.status(500).json({ error: 'Error al actualizar alumno', detalle: err2.message });
      }
      res.status(200).json({ mensaje: 'Alumno actualizado exitosamente' });
    });
  });
});

// DELETE: Eliminar alumno (solo admin)
router.delete('/alumnos/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  Alumno.obtenerPorId(id, (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error interno del servidor', detalle: err.message });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún alumno con ese ID' });
    }

    // Primero: Eliminar todas las inscripciones del alumno (si existen)
    // Esto debe hacerse ANTES de eliminar el alumno para evitar errores de clave foránea
    db.query('DELETE FROM inscripciones WHERE alumno_id = ?', [id], (errInscripciones, resultInscripciones) => {
      if (errInscripciones) {
        console.error('Error al eliminar inscripciones:', errInscripciones);
        return res.status(500).json({ 
          error: 'Error al eliminar inscripciones del alumno', 
          detalle: errInscripciones.message 
        });
      }

      console.log(`Inscripciones eliminadas: ${resultInscripciones ? resultInscripciones.affectedRows : 0}`);

      // Segundo: Ahora sí podemos eliminar el alumno de forma segura
      Alumno.eliminar(id, (errAlumno) => {
        if (errAlumno) {
          // Verificar si es un error de clave foránea
          if (errAlumno.code === 'ER_ROW_IS_REFERENCED_2' || errAlumno.code === '23000' || errAlumno.errno === 1451) {
            return res.status(400).json({ 
              error: 'No se puede eliminar el alumno porque aún tiene inscripciones asociadas. Intenta eliminar las inscripciones manualmente primero.',
              detalle: errAlumno.message
            });
          }
          return res.status(500).json({ 
            error: 'Error al eliminar alumno', 
            detalle: errAlumno.message 
          });
        }

        // Éxito: ambas operaciones completadas
        const inscripcionesEliminadas = resultInscripciones ? resultInscripciones.affectedRows : 0;
        res.status(200).json({ 
          mensaje: 'Alumno eliminado exitosamente',
          inscripciones_eliminadas: inscripcionesEliminadas
        });
      });
    });
  });
});

// POST: Subir foto de alumno (solo admin)
router.post('/alumnos/:id/foto', verificarToken, soloAdmin, uploadAlumno.single('foto'), (req, res) => {
  const id = parseInt(req.params.id);

  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }

  if (!req.file) {
    return res.status(400).json({ error: 'No se ha proporcionado ninguna imagen' });
  }

  Alumno.obtenerPorId(id, (err, resultados) => {
    if (err) {
      return res.status(500).json({ error: 'Error interno del servidor', detalle: err.message });
    }
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún alumno con ese ID' });
    }

    Alumno.actualizarFoto(id, req.file.filename, (err2) => {
      if (err2) {
        return res.status(500).json({ error: 'Error al guardar foto', detalle: err2.message });
      }
      res.status(200).json({
        mensaje: 'Foto actualizada exitosamente',
        foto: req.file.filename,
        foto_url: `${process.env.BASE_URL}/uploads/alumnos/${req.file.filename}`
      });
    });
  });
});

module.exports = router;