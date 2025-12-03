const db = require('../config/db');

const Producto = {
  obtenerTodos: (callback) => {
    const query = 'SELECT * FROM productos WHERE estado = 1 ORDER BY id DESC';
    db.query(query, callback);
  },

  obtenerPorId: (id, callback) => {
    const query = 'SELECT * FROM productos WHERE id = ? AND estado = 1';
    db.query(query, [id], callback);
  },

  crear: (data, callback) => {
    db.query('INSERT INTO productos SET ?', [data], callback);
  },

  actualizar: (id, data, callback) => {
    db.query('UPDATE productos SET ? WHERE id = ?', [data, id], callback);
  },

  eliminar: (id, callback) => {
    db.query('UPDATE productos SET estado = 0 WHERE id = ?', [id], callback);
  },

  actualizarImagen: (id, campoImagen, nombreImagen, callback) => {
    const query = `UPDATE productos SET ${campoImagen} = ? WHERE id = ?`;
    db.query(query, [nombreImagen, id], callback);
  }
};

module.exports = Producto;




