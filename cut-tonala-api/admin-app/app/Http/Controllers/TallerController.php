<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Helpers\BladeHelper;

class TallerController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar lista de talleres
     */
    public function index($request = null)
    {
        try {
            $talleres = $this->apiService->get('/api/talleres');
            
            BladeHelper::render('talleres.index', ['talleres' => $talleres]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorMessage = 'Error al obtener los talleres';

            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents(), true);
                if (isset($response['error'])) {
                    $errorMessage = $response['error'];
                }
            }

            $_SESSION['errors'] = ['error' => $errorMessage];
            header('Location: ' . route('login'));
            exit;
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['error' => 'Error al conectar con el servidor. Verifica que la API esté corriendo.'];
            header('Location: ' . route('login'));
            exit;
        }
    }

    /**
     * Mostrar detalles de un taller
     */
    public function show($request = null, $id = null)
    {
        // Si el id viene como segundo parámetro directo
        if ($id === null && $request !== null && is_string($request) && is_numeric($request)) {
            $id = $request;
            $request = null;
        }
        
        if ($id === null) {
            $_SESSION['errors'] = ['error' => 'ID de taller no proporcionado'];
            header('Location: ' . route('talleres.index'));
            exit;
        }
        
        try {
            $taller = $this->apiService->get("/api/talleres/{$id}");
            
            BladeHelper::render('talleres.show', ['taller' => $taller]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorMessage = 'Error al obtener el taller';

            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents(), true);
                if (isset($response['error'])) {
                    $errorMessage = $response['error'];
                }
            }

            $_SESSION['errors'] = ['error' => $errorMessage];
            header('Location: ' . route('talleres.index'));
            exit;
        }
    }
}

