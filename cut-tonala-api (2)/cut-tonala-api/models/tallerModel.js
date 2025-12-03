const db = require('../config/db');

const Taller = {
  obtenerTodos: (callback) => {
    const query = `
      SELECT t.*, c.nombre AS categoria_nombre, p.nombre AS profesor_nombre
      FROM talleres t
      LEFT JOIN categorias c ON t.categoria_id = c.id
      LEFT JOIN profesores p ON t.profesor_id = p.id
    `;
    db.query(query, callback);
  },

  obtenerPorId: (id, callback) => {
    const query = `
      SELECT t.*, c.nombre AS categoria_nombre, p.nombre AS profesor_nombre, p.email AS profesor_email
      FROM talleres t
      LEFT JOIN categorias c ON t.categoria_id = c.id
      LEFT JOIN profesores p ON t.profesor_id = p.id
      WHERE t.id = ?
    `;
    db.query(query, [id], callback);
  },

  crear: (data, callback) => {
    db.query('INSERT INTO talleres SET ?', [data], callback);
  },

  actualizar: (id, data, callback) => {
    db.query('UPDATE talleres SET ? WHERE id = ?', [data, id], callback);
  },

  eliminar: (id, callback) => {
    db.query('DELETE FROM talleres WHERE id = ?', [id], callback);
  },

  actualizarFoto: (id, nombreFoto, callback) => {
    db.query('UPDATE talleres SET foto = ? WHERE id = ?', [nombreFoto, id], callback);
  },

  obtenerPorCategoria: (categoriaId, callback) => {
    const query = `
      SELECT t.*, p.nombre AS profesor_nombre
      FROM talleres t
      LEFT JOIN profesores p ON t.profesor_id = p.id
      WHERE t.categoria_id = ?
    `;
    db.query(query, [categoriaId], callback);
  },

  obtenerPorProfesor: (profesorId, callback) => {
    const query = `
      SELECT t.*, c.nombre AS categoria_nombre
      FROM talleres t
      LEFT JOIN categorias c ON t.categoria_id = c.id
      WHERE t.profesor_id = ?
    `;
    db.query(query, [profesorId], callback);
  },

  obtenerConInscritos: (id, callback) => {
    const query = `
      SELECT t.*, 
             COUNT(i.id) AS total_inscritos,
             c.nombre AS categoria_nombre,
             p.nombre AS profesor_nombre
      FROM talleres t
      LEFT JOIN inscripciones i ON t.id = i.taller_id AND i.estado = 'activo'
      LEFT JOIN categorias c ON t.categoria_id = c.id
      LEFT JOIN profesores p ON t.profesor_id = p.id
      WHERE t.id = ?
      GROUP BY t.id
    `;
    db.query(query, [id], callback);
  }
};

module.exports = Taller;