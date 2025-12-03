<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthApiService
{
    protected $client;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://localhost:3000');
        
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout' => 10.0,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Registra un nuevo usuario
     */
    public function registrar(array $datos)
    {
        try {
            // La API espera: correo, contrasena, nombre, rol (opcional)
            $datosApi = [
                'correo' => $datos['correo'],
                'contrasena' => $datos['password'],
                'nombre' => $datos['nombre'],
                'rol' => 'alumno' // Por defecto
            ];
            
            $response = $this->client->post('/api/registro', [
                'json' => $datosApi
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            if ($statusCode === 201 || $statusCode === 200) {
                return json_decode($body, true);
            }
            
            // Intentar parsear el error de la API
            $errorData = json_decode($body, true);
            if (is_array($errorData)) {
                $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'Error al registrar';
                if (isset($errorData['errors'])) {
                    // Si hay errores de validación, mostrarlos
                    $errors = is_array($errorData['errors']) ? $errorData['errors'] : [];
                    $errorMessage .= ': ' . implode(', ', array_map(function($field, $messages) {
                        return is_array($messages) ? implode(', ', $messages) : $messages;
                    }, array_keys($errors), $errors));
                }
                return ['error' => $errorMessage];
            }
            
            // Si no es JSON, mostrar el status code
            if ($statusCode === 404) {
                return ['error' => 'Endpoint de registro no encontrado. Verifica la configuración de la API.'];
            }
            
            return ['error' => "Error al registrar (Status: {$statusCode})"];
        } catch (RequestException $e) {
            Log::error('Error al registrar usuario: ' . $e->getMessage());
            return ['error' => 'Error de conexión con la API: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Log::error('Error inesperado al registrar: ' . $e->getMessage());
            return ['error' => 'Error inesperado: ' . $e->getMessage()];
        }
    }

    /**
     * Inicia sesión y obtiene el token
     */
    public function login(array $credenciales)
    {
        try {
            // La API espera: correo, contrasena (o contraseña)
            $credencialesApi = [
                'correo' => $credenciales['correo'],
                'contrasena' => $credenciales['password']
            ];
            
            $response = $this->client->post('/api/login', [
                'json' => $credencialesApi
            ]);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                
                if (isset($data['token'])) {
                    // Guardar token en sesión
                    Session::put('token', $data['token']);
                    Session::put('usuario', $data['usuario'] ?? null);
                    return $data;
                }
            }
            
            $error = json_decode($response->getBody(), true);
            return ['error' => $error['message'] ?? 'Credenciales inválidas'];
        } catch (RequestException $e) {
            Log::error('Error al iniciar sesión: ' . $e->getMessage());
            return ['error' => 'Error de conexión con la API'];
        }
    }

    /**
     * Obtiene el perfil del usuario autenticado
     */
    public function obtenerPerfil()
    {
        try {
            $token = Session::get('token');
            if (!$token) {
                return null;
            }

            $response = $this->client->get('/api/usuarios/perfil', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ]
            ]);
            
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody(), true);
            }
            
            return null;
        } catch (RequestException $e) {
            Log::error('Error al obtener perfil: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene las inscripciones (pedidos) del usuario autenticado
     */
    public function obtenerMisPedidos()
    {
        try {
            $token = Session::get('token');
            if (!$token) {
                return [];
            }

            // Primero obtener el perfil para tener el ID del usuario
            $usuario = $this->obtenerPerfil();
            if (!$usuario || !isset($usuario['id'])) {
                return [];
            }

            // Obtener inscripciones del alumno (en esta API los "pedidos" son inscripciones)
            $response = $this->client->get('/api/inscripciones/alumno/' . $usuario['id'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ]
            ]);
            
            if ($response->getStatusCode() === 200) {
                $inscripciones = json_decode($response->getBody(), true);
                return is_array($inscripciones) ? $inscripciones : [];
            }
            
            return [];
        } catch (RequestException $e) {
            Log::error('Error al obtener inscripciones: ' . $e->getMessage());
            return [];
        } catch (\Exception $e) {
            Log::error('Error inesperado al obtener inscripciones: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Cierra la sesión
     */
    public function logout()
    {
        Session::forget('token');
        Session::forget('usuario');
    }

    /**
     * Verifica si el usuario está autenticado
     */
    public function estaAutenticado()
    {
        return Session::has('token');
    }

    /**
     * Obtiene el token actual
     */
    public function obtenerToken()
    {
        return Session::get('token');
    }

    /**
     * Obtiene los datos del usuario en sesión
     */
    public function obtenerUsuario()
    {
        return Session::get('usuario');
    }
}

