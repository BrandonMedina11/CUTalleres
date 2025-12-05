const db = require('../config/db');

const Informacion = {
  obtenerTodos: (callback) => {
    const query = `
      SELECT i.*, a.nombre AS admin_nombre, a.email AS admin_email
      FROM informacion i
      LEFT JOIN administradores a ON i.admin_id = a.id
    `;
    db.query(query, callback);
  },

  obtenerPorId: (id, callback) => {
    const query = `
      SELECT i.*, a.nombre AS admin_nombre, a.email AS admin_email
      FROM informacion i
      LEFT JOIN administradores a ON i.admin_id = a.id
      WHERE i.id = ?
    `;
    db.query(query, [id], callback);
  },

  crear: (data, callback) => {
    db.query('INSERT INTO informacion SET ?', [data], callback);
  },

  actualizar: (id, data, callback) => {
    db.query('UPDATE informacion SET ? WHERE id = ?', [data, id], callback);
  },

  eliminar: (id, callback) => {
    db.query('DELETE FROM informacion WHERE id = ?', [id], callback);
  },

  obtenerPorTipo: (tipo, callback) => {
    const query = `
      SELECT i.*, a.nombre AS admin_nombre
      FROM informacion i
      LEFT JOIN administradores a ON i.admin_id = a.id
      WHERE i.tipo = ?
    `;
    db.query(query, [tipo], callback);
  },

  obtenerPorAdmin: (adminId, callback) => {
    db.query('SELECT * FROM informacion WHERE admin_id = ?', [adminId], callback);
  }
};

module.exports = Informacion;