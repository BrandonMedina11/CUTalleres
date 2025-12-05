<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ProductoService
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
     * Obtener todos los productos
     */
    public function obtenerProductos()
    {
        try {
            $response = $this->client->get('/api/productos');
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception('Error al obtener productos: ' . $e->getMessage());
        }
    }

    /**
     * Obtener producto por ID
     */
    public function obtenerProductoPorId($id)
    {
        try {
            $response = $this->client->get("/api/productos/{$id}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 404) {
                throw new \Exception('Producto no encontrado');
            }
            throw new \Exception('Error al obtener el producto: ' . $e->getMessage());
        }
    }
}


