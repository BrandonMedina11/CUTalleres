const express = require('express');
const router = express.Router();
const { body, validationResult } = require('express-validator');
const Profesor = require('../models/profesorModel');
const { uploadProfesor } = require('../middlewares/uploadConfig');
const { verificarToken, soloAdmin } = require('../middlewares/authMiddleware');
const db = require('../config/db');

const validarProfesor = [
  body('nombre').notEmpty().withMessage('El nombre es obligatorio').trim(),
  body('email').isEmail().withMessage('Debe proporcionar un email válido'),
  body('telefono').optional().isLength({ min: 10, max: 20 })
];

router.get('/profesores', (req, res) => {
  Profesor.obtenerTodos((err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error al obtener profesores' });
    const profesoresConURL = resultados.map(p => ({
      ...p,
      foto_url: p.foto ? `${process.env.BASE_URL}/uploads/profesores/${p.foto}` : null
    }));
    res.status(200).json(profesoresConURL);
  });
});

router.get('/profesores/:id', (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Profesor.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún profesor con ese ID' });
    }
    const profesor = resultados[0];
    profesor.foto_url = profesor.foto ? `${process.env.BASE_URL}/uploads/profesores/${profesor.foto}` : null;
    res.status(200).json(profesor);
  });
});

router.get('/profesores/:id/talleres', (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Profesor.obtenerConTalleres(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún profesor con ese ID' });
    }
    res.status(200).json(resultados);
  });
});

router.post('/profesores', verificarToken, soloAdmin, validarProfesor, (req, res) => {
  const errores = validationResult(req);
  if (!errores.isEmpty()) {
    return res.status(400).json({ errores: errores.array() });
  }
  const { nombre, email, telefono } = req.body;
  Profesor.buscarPorEmail(email, (err, resultado) => {
    if (err) return res.status(500).json({ error: 'Error al verificar email' });
    if (resultado.length > 0) {
      return res.status(400).json({ error: 'El email ya está registrado' });
    }
    Profesor.crear({ nombre, email, telefono }, (err2, resultado2) => {
      if (err2) return res.status(500).json({ error: 'Error al crear profesor' });
      res.status(201).json({ mensaje: 'Profesor creado exitosamente', id: resultado2.insertId });
    });
  });
});

router.put('/profesores/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Profesor.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún profesor con ese ID' });
    }
    const datosActualizados = {};
    if (req.body.nombre) datosActualizados.nombre = req.body.nombre;
    if (req.body.email) datosActualizados.email = req.body.email;
    if (req.body.telefono) datosActualizados.telefono = req.body.telefono;
    Profesor.actualizar(id, datosActualizados, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al actualizar profesor' });
      res.status(200).json({ mensaje: 'Profesor actualizado exitosamente' });
    });
  });
});

router.delete('/profesores/:id', verificarToken, soloAdmin, (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  Profesor.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún profesor con ese ID' });
    }

    // Primero: Eliminar inscripciones de los talleres del profesor
    // Segundo: Eliminar los talleres del profesor
    // Tercero: Eliminar el profesor
    
    // Paso 1: Obtener IDs de talleres del profesor primero
    db.query('SELECT id FROM talleres WHERE profesor_id = ?', [id], (errTalleresIds, talleresIds) => {
      if (errTalleresIds) {
        console.error('Error al obtener talleres del profesor:', errTalleresIds);
        return res.status(500).json({ 
          error: 'Error al obtener talleres del profesor', 
          detalle: errTalleresIds.message 
        });
      }

      const idsTalleres = talleresIds.map(t => t.id);

      // Si hay talleres, eliminar sus inscripciones
      if (idsTalleres.length > 0) {
        const placeholders = idsTalleres.map(() => '?').join(',');
        db.query(`DELETE FROM inscripciones WHERE taller_id IN (${placeholders})`, idsTalleres, (errInscripciones, resultInscripciones) => {
          if (errInscripciones) {
            console.error('Error al eliminar inscripciones de talleres:', errInscripciones);
            return res.status(500).json({ 
              error: 'Error al eliminar inscripciones de los talleres del profesor', 
              detalle: errInscripciones.message 
            });
          }

          const inscripcionesEliminadas = resultInscripciones ? resultInscripciones.affectedRows : 0;
          console.log(`Inscripciones eliminadas: ${inscripcionesEliminadas}`);

          // Paso 2: Eliminar talleres del profesor
          db.query('DELETE FROM talleres WHERE profesor_id = ?', [id], (errTalleres, resultTalleres) => {
            if (errTalleres) {
              console.error('Error al eliminar talleres:', errTalleres);
              // Verificar si es un error de clave foránea
              if (errTalleres.code === 'ER_ROW_IS_REFERENCED_2' || errTalleres.code === '23000' || errTalleres.errno === 1451) {
                return res.status(400).json({ 
                  error: 'No se puede eliminar el profesor porque tiene talleres asociados. Intenta eliminar los talleres primero.',
                  detalle: errTalleres.message
                });
              }
              return res.status(500).json({ 
                error: 'Error al eliminar talleres del profesor', 
                detalle: errTalleres.message 
              });
            }

            const talleresEliminados = resultTalleres ? resultTalleres.affectedRows : 0;
            console.log(`Talleres eliminados: ${talleresEliminados}`);

            // Paso 3: Eliminar el profesor
            Profesor.eliminar(id, (errProfesor) => {
              if (errProfesor) {
                // Verificar si es un error de clave foránea
                if (errProfesor.code === 'ER_ROW_IS_REFERENCED_2' || errProfesor.code === '23000' || errProfesor.errno === 1451) {
                  return res.status(400).json({ 
                    error: 'No se puede eliminar el profesor porque aún tiene talleres asociados.',
                    detalle: errProfesor.message
                  });
                }
                return res.status(500).json({ 
                  error: 'Error al eliminar profesor', 
                  detalle: errProfesor.message 
                });
              }

              // Éxito: todas las operaciones completadas
              res.status(200).json({ 
                mensaje: 'Profesor eliminado exitosamente',
                talleres_eliminados: talleresEliminados,
                inscripciones_eliminadas: inscripcionesEliminadas
              });
            });
          });
        });
      } else {
        // No hay talleres, eliminar directamente el profesor
        Profesor.eliminar(id, (errProfesor) => {
          if (errProfesor) {
            return res.status(500).json({ 
              error: 'Error al eliminar profesor', 
              detalle: errProfesor.message 
            });
          }

          res.status(200).json({ 
            mensaje: 'Profesor eliminado exitosamente',
            talleres_eliminados: 0,
            inscripciones_eliminadas: 0
          });
        });
      }
    });
  });
});

router.post('/profesores/:id/foto', verificarToken, soloAdmin, uploadProfesor.single('foto'), (req, res) => {
  const id = parseInt(req.params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return res.status(400).json({ error: 'El ID proporcionado no es válido' });
  }
  if (!req.file) {
    return res.status(400).json({ error: 'No se ha proporcionado ninguna imagen' });
  }
  Profesor.obtenerPorId(id, (err, resultados) => {
    if (err) return res.status(500).json({ error: 'Error interno del servidor' });
    if (resultados.length === 0) {
      return res.status(404).json({ error: 'No se encontró ningún profesor con ese ID' });
    }
    Profesor.actualizarFoto(id, req.file.filename, (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al guardar foto' });
      res.status(200).json({
        mensaje: 'Foto actualizada exitosamente',
        foto: req.file.filename,
        foto_url: `${process.env.BASE_URL}/uploads/profesores/${req.file.filename}`
      });
    });
  });
});

module.exports = router;