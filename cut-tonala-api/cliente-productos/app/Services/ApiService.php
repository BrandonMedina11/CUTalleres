<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url', 'http://localhost:3000');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => config('api.timeout', 30),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Realizar petición POST
     */
    public function post($endpoint, $data = [])
    {
        try {
            $response = $this->client->post($endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = $e->getResponse()->getBody()->getContents();
                $response = json_decode($responseBody, true);
                
                if ($statusCode === 404) {
                    throw new \Exception('Endpoint de registro no encontrado. Verifica la configuración de la API.');
                }
                
                // Si la respuesta no es JSON válido, usar el mensaje de error original
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Error al conectar con la API. Código: ' . $statusCode);
                }
                
                throw new \Exception($response['error'] ?? 'Error al conectar con la API');
            }
            
            // Si no hay respuesta, probablemente la API no está corriendo
            $errorMessage = $e->getMessage();
            
            // Detectar diferentes tipos de errores de conexión
            if (strpos($errorMessage, 'Connection refused') !== false || 
                strpos($errorMessage, 'Failed to connect') !== false ||
                strpos($errorMessage, 'cURL error 7') !== false ||
                strpos($errorMessage, 'Could not resolve host') !== false) {
                throw new \Exception('No se pudo conectar con la API en ' . $this->baseUrl . '. Verifica que el servidor esté corriendo.');
            }
            
            if (strpos($errorMessage, 'timeout') !== false || strpos($errorMessage, 'timed out') !== false) {
                throw new \Exception('La conexión con la API tardó demasiado. Verifica que el servidor esté respondiendo.');
            }
            
            throw new \Exception('Error al conectar con la API: ' . $errorMessage);
        } catch (\Exception $e) {
            // Re-lanzar si ya es nuestra excepción personalizada
            if (strpos($e->getMessage(), 'No se pudo conectar') !== false ||
                strpos($e->getMessage(), 'Error al conectar') !== false ||
                strpos($e->getMessage(), 'Endpoint de registro') !== false) {
                throw $e;
            }
            
            // Capturar cualquier otro tipo de error
            if (strpos($e->getMessage(), 'Connection') !== false || 
                strpos($e->getMessage(), 'connect') !== false) {
                throw new \Exception('No se pudo conectar con la API. Verifica que el servidor esté corriendo en ' . $this->baseUrl);
            }
            throw $e;
        }
    }

    /**
     * Realizar petición GET
     */
    public function get($endpoint, $params = [])
    {
        try {
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'query' => $params,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents(), true);
                throw new \Exception($response['error'] ?? 'Error al conectar con la API');
            }
            throw new \Exception('Error al conectar con el servidor. Verifica que la API esté corriendo.');
        }
    }
}

