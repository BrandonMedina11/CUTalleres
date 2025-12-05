const db = require('../config/db');

const Administrador = {
  obtenerTodos: (callback) => {
    db.query('SELECT id, nombre, email, usuario, telefono, foto, estado FROM administradores', callback);
  },

  obtenerPorId: (id, callback) => {
    db.query(
      'SELECT id, nombre, email, usuario, telefono, foto, estado FROM administradores WHERE id = ?',
      [id],
      callback
    );
  },

  crear: (data, callback) => {
    db.query('INSERT INTO administradores SET ?', [data], callback);
  },

  actualizar: (id, data, callback) => {
    db.query('UPDATE administradores SET ? WHERE id = ?', [data, id], callback);
  },

  eliminar: (id, callback) => {
    db.query('DELETE FROM administradores WHERE id = ?', [id], callback);
  },

  actualizarFoto: (id, nombreFoto, callback) => {
    db.query('UPDATE administradores SET foto = ? WHERE id = ?', [nombreFoto, id], callback);
  },

  buscarPorEmail: (email, callback) => {
    db.query('SELECT * FROM administradores WHERE email = ?', [email], callback);
  },

  buscarPorUsuario: (usuario, callback) => {
    db.query('SELECT * FROM administradores WHERE usuario = ?', [usuario], callback);
  }
};

module.exports = Administrador;