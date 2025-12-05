const db = require('../config/db');

const Alumno = {
  obtenerTodos: (callback) => {
    db.query('SELECT * FROM alumnos', callback);
  },

  obtenerPorId: (id, callback) => {
    db.query('SELECT * FROM alumnos WHERE id = ?', [id], callback);
  },

  crear: (data, callback) => {
    db.query('INSERT INTO alumnos SET ?', [data], callback);
  },

  actualizar: (id, data, callback) => {
    db.query('UPDATE alumnos SET ? WHERE id = ?', [data, id], callback);
  },

  eliminar: (id, callback) => {
    db.query('DELETE FROM alumnos WHERE id = ?', [id], callback);
  },

  actualizarFoto: (id, nombreFoto, callback) => {
    db.query('UPDATE alumnos SET foto = ? WHERE id = ?', [nombreFoto, id], callback);
  },

  buscarPorCodigo: (codigo, callback) => {
    db.query('SELECT * FROM alumnos WHERE codigo = ?', [codigo], callback);
  },

  buscarPorEmail: (email, callback) => {
    db.query('SELECT * FROM alumnos WHERE email = ?', [email], callback);
  },

  obtenerConTalleres: (id, callback) => {
    const query = `
      SELECT a.*, t.nombre AS taller, i.fecha_registro, i.estado AS estado_inscripcion
      FROM alumnos a
      LEFT JOIN inscripciones i ON a.id = i.alumno_id
      LEFT JOIN talleres t ON i.taller_id = t.id
      WHERE a.id = ?
    `;
    db.query(query, [id], callback);
  }
};

module.exports = Alumno;