const multer = require('multer');
const path = require('path');
const fs = require('fs');

// Función para crear configuración de almacenamiento por entidad
const createStorage = (folder) => {
  return multer.diskStorage({
    destination: (req, file, cb) => {
      const uploadPath = `./uploads/${folder}`;
      if (!fs.existsSync(uploadPath)) {
        fs.mkdirSync(uploadPath, { recursive: true });
      }
      cb(null, uploadPath);
    },
    filename: (req, file, cb) => {
      const id = req.params.id || Date.now();
      const ext = path.extname(file.originalname);
      const filename = `${folder}_${id}_${Date.now()}${ext}`;
      cb(null, filename);
    }
  });
};

// Filtro para validar tipos de archivo
const fileFilter = (req, file, cb) => {
  const allowedTypes = /jpeg|jpg|png|gif/;
  const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
  const mimetype = allowedTypes.test(file.mimetype);

  if (extname && mimetype) {
    cb(null, true);
  } else {
    cb(new Error('Solo se permiten imágenes (JPEG, JPG, PNG, GIF)'));
  }
};

// Crear configuraciones de upload para cada entidad
const uploadAdministrador = multer({
  storage: createStorage('administradores'),
  limits: { fileSize: 5 * 1024 * 1024 },
  fileFilter
});

const uploadAlumno = multer({
  storage: createStorage('alumnos'),
  limits: { fileSize: 5 * 1024 * 1024 },
  fileFilter
});

const uploadProfesor = multer({
  storage: createStorage('profesores'),
  limits: { fileSize: 5 * 1024 * 1024 },
  fileFilter
});

const uploadCategoria = multer({
  storage: createStorage('categorias'),
  limits: { fileSize: 5 * 1024 * 1024 },
  fileFilter
});

const uploadTaller = multer({
  storage: createStorage('talleres'),
  limits: { fileSize: 5 * 1024 * 1024 },
  fileFilter
});

const uploadProducto = multer({
  storage: createStorage('productos'),
  limits: { fileSize: 5 * 1024 * 1024 },
  fileFilter
});

module.exports = {
  uploadAdministrador,
  uploadAlumno,
  uploadProfesor,
  uploadCategoria,
  uploadTaller,
  uploadProducto
};