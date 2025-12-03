<?php

namespace App\Http\Controllers;

use App\Services\ProductoApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    protected $productoService;

    public function __construct(ProductoApiService $productoService)
    {
        $this->productoService = $productoService;
    }

    public function home()
    {
        return view('home');
    }

    public function catalogo()
    {
        try {
            $productos = $this->productoService->obtenerProductos();
            $cargando = false;
            $error = null;
            
            // Si no hay productos, verificar si es un error de conexión o simplemente no hay datos
            if (empty($productos)) {
                // Verificar los logs para ver si fue un error 401
                $apiBaseUrl = $this->productoService->getApiBaseUrl();
                $error = 'No se pudo cargar el catálogo de talleres. ';
                $error .= 'La API requiere autenticación. Verifica que tengas configurado el token en el archivo .env (API_TOKEN=tu-token). ';
                $error .= 'API: ' . $apiBaseUrl;
            }
            
            return view('catalogo', [
                'productos' => $productos,
                'error' => $error,
                'cargando' => $cargando,
                'apiBaseUrl' => $this->productoService->getApiBaseUrl()
            ]);
        } catch (\Exception $e) {
            Log::error('Error en controlador catalogo: ' . $e->getMessage());
            return view('catalogo', [
                'productos' => [],
                'error' => 'Error al cargar el catálogo de talleres. Por favor, verifica la conexión con la API.',
                'cargando' => false,
                'apiBaseUrl' => $this->productoService->getApiBaseUrl()
            ]);
        }
    }

    public function detalle($id)
    {
        try {
            $producto = $this->productoService->obtenerProductoPorId($id);
            
            if (!$producto || (isset($producto['estado']) && !$producto['estado'])) {
                return view('not-found');
            }
            
            return view('detalle-producto', [
                'producto' => $producto,
                'cargando' => false,
                'error' => null,
                'apiBaseUrl' => $this->productoService->getApiBaseUrl()
            ]);
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function notFound()
    {
        return view('not-found');
    }
}
