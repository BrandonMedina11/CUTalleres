const jwt = require('jsonwebtoken');

// Middleware para verificar que el token sea válido
exports.verificarToken = (req, res, next) => {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1]; // Bearer TOKEN

  if (!token) {
    return res.status(401).json({ error: 'Token requerido. Inicia sesión primero.' });
  }

  jwt.verify(token, process.env.JWT_SECRET, (err, usuario) => {
    if (err) {
      return res.status(403).json({ error: 'Token inválido o expirado' });
    }
    
    // Guardar información del usuario en la request
    req.usuario = usuario;
    next();
  });
};

// Middleware para verificar que sea ADMIN
exports.soloAdmin = (req, res, next) => {
  if (req.usuario.rol !== 'admin') {
    return res.status(403).json({ error: 'Acceso restringido solo para administradores' });
  }
  next();
};

// Middleware para verificar que sea CLIENTE (según especificaciones)
exports.soloCliente = (req, res, next) => {
  if (req.usuario.rol !== 'cliente') {
    return res.status(403).json({ error: 'Acceso restringido a clientes' });
  }
  next();
};

// Middleware para permitir ADMIN o el mismo usuario
exports.adminOPropio = (req, res, next) => {
  const idRuta = parseInt(req.params.id);
  
  if (req.usuario.rol === 'admin' || req.usuario.id === idRuta) {
    next();
  } else {
    return res.status(403).json({ error: 'No tienes permiso para acceder a este recurso' });
  }
};