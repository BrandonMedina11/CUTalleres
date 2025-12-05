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
     * Obtener el token de autenticación desde la sesión
     */
    protected function getToken()
    {
        return $_SESSION['api_token'] ?? null;
    }

    /**
     * Agregar el token de autenticación a los headers
     */
    protected function getHeaders()
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $token = $this->getToken();
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }

    /**
     * Realizar petición GET
     */
    public function get($endpoint, $params = [])
    {
        try {
            $response = $this->client->get($endpoint, [
                'headers' => $this->getHeaders(),
                'query' => $params,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->handleError($e);
            throw $e;
        }
    }

    /**
     * Realizar petición POST
     */
    public function post($endpoint, $data = [])
    {
        try {
            $response = $this->client->post($endpoint, [
                'headers' => $this->getHeaders(),
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->handleError($e);
            throw $e;
        }
    }

    /**
     * Realizar petición PUT
     */
    public function put($endpoint, $data = [])
    {
        try {
            $response = $this->client->put($endpoint, [
                'headers' => $this->getHeaders(),
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->handleError($e);
            throw $e;
        }
    }

    /**
     * Realizar petición DELETE
     */
    public function delete($endpoint)
    {
        try {
            $response = $this->client->delete($endpoint, [
                'headers' => $this->getHeaders(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->handleError($e);
            throw $e;
        }
    }

    /**
     * Manejar errores de la API
     */
    protected function handleError(RequestException $e)
    {
        if ($e->hasResponse()) {
            $statusCode = $e->getResponse()->getStatusCode();
            
            // Si el token es inválido o expiró, limpiar sesión
            if ($statusCode === 401 || $statusCode === 403) {
                unset($_SESSION['api_token']);
                unset($_SESSION['usuario']);
            }
        }
    }
}

