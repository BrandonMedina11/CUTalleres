<?php

namespace App\Http\Controllers;

use App\Services\ProductoApiService;
use App\Services\CarritoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    protected $productoService;
    protected $carritoService;

    public function __construct(ProductoApiService $productoService, CarritoService $carritoService)
    {
        $this->productoService = $productoService;
        $this->carritoService = $carritoService;
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
            
            // Obtener IDs de talleres en el carrito
            $talleresEnCarrito = array_column($this->carritoService->obtener(), 'id');
            
            return view('catalogo', [
                'productos' => $productos,
                'error' => $error,
                'cargando' => $cargando,
                'apiBaseUrl' => $this->productoService->getApiBaseUrl(),
                'talleresEnCarrito' => $talleresEnCarrito
            ]);
        } catch (\Exception $e) {
            Log::error('Error en controlador catalogo: ' . $e->getMessage());
            return view('catalogo', [
                'productos' => [],
                'error' => 'Error al cargar el catálogo de talleres. Por favor, verifica la conexión con la API.',
                'cargando' => false,
                'apiBaseUrl' => $this->productoService->getApiBaseUrl(),
                'talleresEnCarrito' => []
            ]);
        }
    }

    public function detalle($id)
    {
        try {
            // Intentar obtener el producto desde la API
            $producto = $this->productoService->obtenerProductoPorId($id);
            
            // Si la API no responde, buscar en la lista completa de productos
            if (!$producto) {
                $todosLosProductos = $this->productoService->obtenerProductos();
                
                foreach ($todosLosProductos as $p) {
                    if (isset($p['id']) && $p['id'] == $id) {
                        $producto = $p;
                        break;
                    }
                }
            }
            
            // Si aún no se encuentra, mostrar página 404
            if (!$producto || (isset($producto['estado']) && !$producto['estado'])) {
                Log::warning("Producto con ID {$id} no encontrado");
                return view('not-found');
            }
            
            $estaEnCarrito = $this->carritoService->existeEnCarrito($id);
            
            return view('detalle-producto', [
                'producto' => $producto,
                'cargando' => false,
                'error' => null,
                'apiBaseUrl' => $this->productoService->getApiBaseUrl(),
                'estaEnCarrito' => $estaEnCarrito
            ]);
        } catch (\Exception $e) {
            Log::error('Error en controlador detalle: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return view('not-found');
        }
    }

    public function notFound()
    {
        return view('not-found');
    }
}
