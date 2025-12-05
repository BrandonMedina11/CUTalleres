<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ProductoApiService
{
    protected $client;
    protected $apiUrl;

    public function __construct()
    {
        // URL base de la API cut-tonala-api
        $apiBaseUrl = env('API_URL', 'http://cut-tonala-api.test');
        $this->apiUrl = $apiBaseUrl . '/api/talleres';
        
        // Headers para la petición (si la API requiere autenticación, agregar aquí)
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        
        // Si hay un token de API configurado, agregarlo
        $apiToken = env('API_TOKEN');
        if ($apiToken) {
            $headers['Authorization'] = 'Bearer ' . trim($apiToken);
        }
        
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout' => 10.0,
            'http_errors' => false,
            'headers' => $headers,
        ]);
    }
    
    /**
     * Obtiene la URL base de la API para las imágenes
     */
    public function getApiBaseUrl()
    {
        return env('API_URL', 'http://cut-tonala-api.test');
    }

    public function obtenerProductos()
    {
        try {
            $response = $this->client->get('');
            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 200) {
                $talleres = json_decode($response->getBody(), true);
                
                // Los talleres vienen directamente de la API, no necesitan filtro de estado
                if (is_array($talleres)) {
                    return array_values($talleres); // Reindexar el array
                }
            } elseif ($statusCode === 401) {
                Log::warning("Error 401: No autorizado. La API requiere autenticación. URL: {$this->apiUrl}");
            } else {
                Log::warning("Error al obtener talleres. Status code: {$statusCode}. URL: {$this->apiUrl}");
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Error al obtener talleres: ' . $e->getMessage());
            Log::error('URL intentada: ' . $this->apiUrl);
            return [];
        }
    }

    public function obtenerProductoPorId($id)
    {
        try {
            $response = $this->client->get("/{$id}");
            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 200) {
                return json_decode($response->getBody(), true);
            }
            
            // Registrar el error con más detalles
            $body = $response->getBody()->getContents();
            Log::warning("Error al obtener taller {$id}. Status code: {$statusCode}. URL: {$this->apiUrl}/{$id}");
            Log::warning("Respuesta de la API: " . $body);
            
            if ($statusCode === 401) {
                Log::error("Error 401: No autorizado. La API requiere autenticación. Verifica que tengas configurado el token en el archivo .env (API_TOKEN=tu-token).");
            } elseif ($statusCode === 404) {
                Log::warning("Taller con ID {$id} no encontrado en la API.");
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("Error al obtener taller {$id}: " . $e->getMessage());
            Log::error("URL intentada: {$this->apiUrl}/{$id}");
            return null;
        }
    }
}