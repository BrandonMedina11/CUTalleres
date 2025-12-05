<?php

namespace App\Http\Controllers;

use App\Services\CarritoService;
use App\Services\AuthApiService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfirmacionController extends Controller
{
    protected $carritoService;
    protected $authService;

    public function __construct(CarritoService $carritoService, AuthApiService $authService)
    {
        $this->carritoService = $carritoService;
        $this->authService = $authService;
    }

    /**
     * Muestra la página de confirmación de inscripciones
     */
    public function index()
    {
        if (!$this->authService->estaAutenticado()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para confirmar tus inscripciones.');
        }

        $talleres = $this->carritoService->obtener();

        if (empty($talleres)) {
            return redirect()->route('carrito')->with('error', 'No hay talleres en tu carrito de inscripciones.');
        }

        return view('confirmacion', [
            'talleres' => $talleres,
            'total' => count($talleres)
        ]);
    }

    /**
     * Confirma las inscripciones de los talleres en el carrito
     */
    public function confirmar(Request $request)
    {
        if (!$this->authService->estaAutenticado()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para confirmar tus inscripciones.');
        }

        $talleres = $this->carritoService->obtener();

        if (empty($talleres)) {
            return redirect()->route('carrito')->with('error', 'No hay talleres en tu carrito de inscripciones.');
        }

        try {
            $token = $this->authService->obtenerToken();
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Obtener el perfil del usuario para tener su ID
            $usuario = $this->authService->obtenerPerfil();
            if (!$usuario || !isset($usuario['id'])) {
                return redirect()->route('login')->with('error', 'No se pudo obtener la información del usuario.');
            }

            $apiUrl = env('API_URL', 'http://localhost:3000');
            $client = new Client([
                'base_uri' => $apiUrl,
                'timeout' => 10.0,
                'http_errors' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ]
            ]);

            // Crear inscripciones para cada taller en el carrito
            $inscripcionesExitosas = 0;
            $errores = [];

            foreach ($talleres as $taller) {
                try {
                    $response = $client->post('/api/inscripciones', [
                        'json' => [
                            'alumno_id' => $usuario['id'],
                            'taller_id' => $taller['id'],
                            'fecha_registro' => date('Y-m-d'), // Fecha actual en formato YYYY-MM-DD
                            'estado' => 'activo'
                        ]
                    ]);

                    $statusCode = $response->getStatusCode();
                    $responseBody = $response->getBody()->getContents();
                    
                    if ($statusCode === 201 || $statusCode === 200) {
                        $inscripcionesExitosas++;
                    } else {
                        $errorData = json_decode($responseBody, true);
                        
                        // Manejar diferentes formatos de error de la API
                        if (isset($errorData['errores']) && is_array($errorData['errores'])) {
                            // Si hay errores de validación
                            $mensajesError = array_map(function($error) {
                                return $error['msg'] ?? $error['message'] ?? 'Error de validación';
                            }, $errorData['errores']);
                            $mensajeError = implode(', ', $mensajesError);
                        } elseif (isset($errorData['error'])) {
                            $mensajeError = $errorData['error'];
                        } elseif (isset($errorData['message'])) {
                            $mensajeError = $errorData['message'];
                        } else {
                            $mensajeError = "Error desconocido (Status: {$statusCode})";
                        }
                        
                        $errores[] = "Taller '{$taller['nombre']}': {$mensajeError}";
                        Log::warning("Error al inscribirse en taller {$taller['id']} (Status {$statusCode}): {$mensajeError}");
                        Log::warning("Respuesta completa: " . $responseBody);
                    }
                } catch (\Exception $e) {
                    $errores[] = "Taller '{$taller['nombre']}': " . $e->getMessage();
                    Log::error("Error al crear inscripción para taller {$taller['id']}: " . $e->getMessage());
                }
            }

            // Limpiar el carrito solo si todas las inscripciones fueron exitosas
            if ($inscripcionesExitosas === count($talleres)) {
                $this->carritoService->limpiar();
                return redirect()->route('carrito')->with('success', '¡Todas tus inscripciones se han confirmado exitosamente!');
            } elseif ($inscripcionesExitosas > 0) {
                // Algunas inscripciones fueron exitosas pero no todas
                $mensaje = "Se confirmaron {$inscripcionesExitosas} de " . count($talleres) . " inscripciones.";
                if (!empty($errores)) {
                    $mensaje .= " Errores: " . implode('; ', $errores);
                }
                return redirect()->route('carrito')->with('error', $mensaje);
            } else {
                // Ninguna inscripción fue exitosa
                $mensaje = "No se pudo confirmar ninguna inscripción.";
                if (!empty($errores)) {
                    $mensaje .= " Errores: " . implode('; ', $errores);
                }
                return redirect()->route('carrito')->with('error', $mensaje);
            }

        } catch (\Exception $e) {
            Log::error('Error al confirmar inscripciones: ' . $e->getMessage());
            return redirect()->route('carrito')->with('error', 'Error al confirmar las inscripciones. Por favor, intenta nuevamente.');
        }
    }
}

