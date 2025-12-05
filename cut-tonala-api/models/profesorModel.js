const db = require('../config/db');

const Profesor = {
  obtenerTodos: (callback) => {
    db.query('SELECT * FROM profesores', callback);
  },

  obtenerPorId: (id, callback) => {
    db.query('SELECT * FROM profesores WHERE id = ?', [id], callback);
  },

  crear: (data, callback) => {
    db.query('INSERT INTO profesores SET ?', [data], callback);
  },

  actualizar: (id, data, callback) => {
    db.query('UPDATE profesores SET ? WHERE id = ?', [data, id], callback);
  },

  eliminar: (id, callback) => {
    db.query('DELETE FROM profesores WHERE id = ?', [id], callback);
  },

  actualizarFoto: (id, nombreFoto, callback) => {
    db.query('UPDATE profesores SET foto = ? WHERE id = ?', [nombreFoto, id], callback);
  },

  buscarPorEmail: (email, callback) => {
    db.query('SELECT * FROM profesores WHERE email = ?', [email], callback);
  },

  obtenerConTalleres: (id, callback) => {
    const query = `
      SELECT p.*, t.id AS taller_id, t.nombre AS taller_nombre, t.descripcion
      FROM profesores p
      LEFT JOIN talleres t ON p.id = t.profesor_id
      WHERE p.id = ?
    `;
    db.query(query, [id], callback);
  }
};

module.exports = Profesor;