<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Helpers\BladeHelper;

class AuthController extends Controller
{
    protected $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegisterForm()
    {
        // Limpiar errores antiguos si es una carga nueva (no un redirect después de error)
        if (!isset($_POST['_token'])) {
            // No limpiar si hay datos antiguos que preservar
            // Los errores se mostrarán si existen en la sesión
        }
        
        BladeHelper::render('auth.registro', []);
    }

    /**
     * Procesar registro
     */
    public function register()
    {
        $request = request();

        // Validaciones básicas
        $nombre = $request->input('nombre');
        $correo = $request->input('correo');
        $contrasena = $request->input('contrasena');
        $contrasena_confirmacion = $request->input('contrasena_confirmacion');

        $errors = [];

        if (empty($nombre)) {
            $errors[] = 'El nombre completo es obligatorio';
        }

        if (empty($correo)) {
            $errors[] = 'El correo electrónico es obligatorio';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo electrónico no es válido';
        }

        if (empty($contrasena)) {
            $errors[] = 'La contraseña es obligatoria';
        } elseif (strlen($contrasena) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }

        if ($contrasena !== $contrasena_confirmacion) {
            $errors[] = 'Las contraseñas no coinciden';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['_old_input'] = $request->only('nombre', 'correo');
            header('Location: ' . route('registro'));
            exit;
        }

        try {
            // Llamar a la API para registrar
            $response = $this->apiService->post('/api/registro', [
                'nombre' => $nombre,
                'correo' => $correo,
                'contrasena' => $contrasena,
                'rol' => 'alumno', // Por defecto, los usuarios se registran como alumnos
            ]);

            $_SESSION['success'] = '¡Registro exitoso! Ahora puedes iniciar sesión.';
            header('Location: ' . route('home'));
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['_old_input'] = $request->only('nombre', 'correo');
            header('Location: ' . route('registro'));
            exit;
        }
    }
}

