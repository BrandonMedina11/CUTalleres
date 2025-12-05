const db = require('../config/db');

const Categoria = {
  obtenerTodos: (callback) => {
    db.query('SELECT * FROM categorias', callback);
  },

  obtenerPorId: (id, callback) => {
    db.query('SELECT * FROM categorias WHERE id = ?', [id], callback);
  },

  crear: (data, callback) => {
    db.query('INSERT INTO categorias SET ?', [data], callback);
  },

  actualizar: (id, data, callback) => {
    db.query('UPDATE categorias SET ? WHERE id = ?', [data, id], callback);
  },

  eliminar: (id, callback) => {
    db.query('DELETE FROM categorias WHERE id = ?', [id], callback);
  },

  actualizarFoto: (id, nombreFoto, callback) => {
    db.query('UPDATE categorias SET foto = ? WHERE id = ?', [nombreFoto, id], callback);
  },

  obtenerConTalleres: (id, callback) => {
    const query = `
      SELECT c.*, t.id AS taller_id, t.nombre AS taller_nombre, t.descripcion
      FROM categorias c
      LEFT JOIN talleres t ON c.id = t.categoria_id
      WHERE c.id = ?
    `;
    db.query(query, [id], callback);
  }
};

module.exports = Categoria;