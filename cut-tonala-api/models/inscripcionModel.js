const db = require('../config/db');

const Inscripcion = {
  obtenerTodos: (callback) => {
    const query = `
      SELECT i.*, 
             a.nombre AS alumno_nombre, a.codigo AS alumno_codigo,
             t.nombre AS taller_nombre
      FROM inscripciones i
      LEFT JOIN alumnos a ON i.alumno_id = a.id
      LEFT JOIN talleres t ON i.taller_id = t.id
    `;
    db.query(query, callback);
  },

  obtenerPorId: (id, callback) => {
    const query = `
      SELECT i.*, 
             a.nombre AS alumno_nombre, a.codigo AS alumno_codigo, a.email AS alumno_email,
             t.nombre AS taller_nombre, t.descripcion AS taller_descripcion,
             p.nombre AS profesor_nombre
      FROM inscripciones i
      LEFT JOIN alumnos a ON i.alumno_id = a.id
      LEFT JOIN talleres t ON i.taller_id = t.id
      LEFT JOIN profesores p ON t.profesor_id = p.id
      WHERE i.id = ?
    `;
    db.query(query, [id], callback);
  },

  crear: (data, callback) => {
    db.query('INSERT INTO inscripciones SET ?', [data], callback);
  },

  actualizar: (id, data, callback) => {
    db.query('UPDATE inscripciones SET ? WHERE id = ?', [data, id], callback);
  },

  eliminar: (id, callback) => {
    db.query('DELETE FROM inscripciones WHERE id = ?', [id], callback);
  },

  obtenerPorAlumno: (alumnoId, callback) => {
    const query = `
      SELECT i.*, t.nombre AS taller_nombre, p.nombre AS profesor_nombre
      FROM inscripciones i
      LEFT JOIN talleres t ON i.taller_id = t.id
      LEFT JOIN profesores p ON t.profesor_id = p.id
      WHERE i.alumno_id = ?
    `;
    db.query(query, [alumnoId], callback);
  },

  obtenerPorTaller: (tallerId, callback) => {
    const query = `
      SELECT i.*, a.nombre AS alumno_nombre, a.codigo AS alumno_codigo, a.email AS alumno_email
      FROM inscripciones i
      LEFT JOIN alumnos a ON i.alumno_id = a.id
      WHERE i.taller_id = ?
    `;
    db.query(query, [tallerId], callback);
  },

  verificarInscripcionExistente: (alumnoId, tallerId, callback) => {
    db.query(
      'SELECT * FROM inscripciones WHERE alumno_id = ? AND taller_id = ? AND estado = "activo"',
      [alumnoId, tallerId],
      callback
    );
  }
};

module.exports = Inscripcion;