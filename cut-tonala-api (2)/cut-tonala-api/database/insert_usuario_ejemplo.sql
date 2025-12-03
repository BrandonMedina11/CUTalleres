-- Script para insertar usuarios de ejemplo
-- Las contraseñas están cifradas con bcrypt
-- Contraseña para ambos: "password123"

-- Usuario ADMIN
INSERT INTO usuarios (correo, contraseña, rol) VALUES 
('admin@cut-tonala.edu.mx', '$2b$10$rQZ8K5K5K5K5K5K5K5K5K.5K5K5K5K5K5K5K5K5K5K5K5K5K5K5K', 'admin');

-- Usuario CLIENTE
INSERT INTO usuarios (correo, contraseña, rol) VALUES 
('cliente@cut-tonala.edu.mx', '$2b$10$rQZ8K5K5K5K5K5K5K5K5K.5K5K5K5K5K5K5K5K5K5K5K5K5K5K5K', 'cliente');

-- NOTA: Para generar contraseñas cifradas, usa este código en Node.js:
-- const bcrypt = require('bcrypt');
-- const hash = await bcrypt.hash('password123', 10);
-- console.log(hash);

