<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Helpers\BladeHelper;

class AuthController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar formulario de login
     */
    public function showLoginForm($request = null)
    {
        // Si ya está autenticado, redirigir a la página principal
        if (session('api_token')) {
            header('Location: ' . route('talleres.index'));
            exit;
        }

        BladeHelper::render('auth.login');
    }

    /**
     * Procesar login
     */
    public function login($request = null)
    {
        if (!$request) {
            $request = request();
        }

        if (!$request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required|min:6',
        ], [
            'correo.required' => 'El correo es obligatorio',
            'correo.email' => 'El correo debe ser válido',
            'contrasena.required' => 'La contraseña es obligatoria',
            'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres',
        ])) {
            $_SESSION['_old_input'] = $request->only('correo');
            header('Location: ' . route('login'));
            exit;
        }

        try {
            // Llamar a la API para autenticar
            $response = $this->apiService->post('/api/login', [
                'correo' => $request->input('correo'),
                'contrasena' => $request->input('contrasena'),
            ]);

            // Guardar token y datos del usuario en la sesión
            $_SESSION['api_token'] = $response['token'];
            $_SESSION['usuario'] = $response['usuario'];
            $_SESSION['success'] = '¡Bienvenido! Has iniciado sesión correctamente.';

            header('Location: ' . route('talleres.index'));
            exit;

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorMessage = 'Credenciales inválidas';

            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents(), true);
                if (isset($response['error'])) {
                    $errorMessage = $response['error'];
                }
            }

            $_SESSION['_old_input'] = $request->only('correo');
            $_SESSION['errors'] = ['error' => $errorMessage];
            header('Location: ' . route('login'));
            exit;
        } catch (\Exception $e) {
            $_SESSION['_old_input'] = $request->only('correo');
            $_SESSION['errors'] = ['error' => 'Error al conectar con el servidor. Verifica que la API esté corriendo.'];
            header('Location: ' . route('login'));
            exit;
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout($request = null)
    {
        unset($_SESSION['api_token']);
        unset($_SESSION['usuario']);
        $_SESSION['success'] = 'Has cerrado sesión correctamente.';

        header('Location: ' . route('login'));
        exit;
    }
}

