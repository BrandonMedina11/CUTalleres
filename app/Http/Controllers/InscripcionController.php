<?php

namespace App\Http\Controllers;

use App\Services\AuthApiService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InscripcionController extends Controller
{
    protected $authService;

    public function __construct(AuthApiService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Inscribe al usuario autenticado en un taller
     */
    public function inscribir(Request $request, $tallerId)
    {
        // Verificar autenticación
        if (!$this->authService->estaAutenticado()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para inscribirte en un taller.');
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

            // Crear la inscripción
            $response = $client->post('/api/inscripciones', [
                'json' => [
                    'alumno_id' => $usuario['id'],
                    'taller_id' => $tallerId,
                ]
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 201 || $statusCode === 200) {
                return redirect()->route('mis-pedidos')->with('success', '¡Te has inscrito exitosamente en el taller!');
            } else {
                $errorData = json_decode($response->getBody()->getContents(), true);
                $mensajeError = $errorData['message'] ?? 'Error al realizar la inscripción.';
                
                Log::warning("Error al inscribirse en taller {$tallerId}: {$mensajeError}");
                
                return redirect()->back()->with('error', $mensajeError);
            }

        } catch (\Exception $e) {
            Log::error('Error al inscribirse en taller: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al realizar la inscripción. Por favor, intenta nuevamente.');
        }
    }
}

