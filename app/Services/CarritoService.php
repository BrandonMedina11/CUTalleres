<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CarritoService
{
    protected $sessionKey = 'carrito_inscripciones';

    /**
     * Agrega un taller al carrito de inscripciones
     */
    public function agregar(array $taller): void
    {
        $carrito = $this->obtener();
        
        // Verificar si el taller ya está en el carrito
        $existe = false;
        foreach ($carrito as $index => $item) {
            if ($item['id'] === $taller['id']) {
                $existe = true;
                break;
            }
        }
        
        if (!$existe) {
            // Agregar el taller con información necesaria
            $carrito[] = [
                'id' => $taller['id'],
                'nombre' => $taller['nombre'] ?? '',
                'descripcion' => $taller['descripcion'] ?? '',
                'categoria_nombre' => $taller['categoria_nombre'] ?? '',
                'profesor_nombre' => $taller['profesor_nombre'] ?? '',
                'foto' => $taller['foto'] ?? null,
                'foto_url' => $taller['foto_url'] ?? null,
            ];
            
            Session::put($this->sessionKey, $carrito);
        }
    }

    /**
     * Obtiene todos los talleres en el carrito
     */
    public function obtener(): array
    {
        return Session::get($this->sessionKey, []);
    }

    /**
     * Elimina un taller del carrito por su ID
     */
    public function eliminar(int $id): void
    {
        $carrito = $this->obtener();
        $carrito = array_filter($carrito, function ($taller) use ($id) {
            return $taller['id'] !== $id;
        });
        
        Session::put($this->sessionKey, array_values($carrito));
    }

    /**
     * Limpia todo el carrito
     */
    public function limpiar(): void
    {
        Session::forget($this->sessionKey);
    }

    /**
     * Obtiene el número de talleres en el carrito
     */
    public function contar(): int
    {
        return count($this->obtener());
    }

    /**
     * Verifica si un taller está en el carrito
     */
    public function existeEnCarrito(int $tallerId): bool
    {
        $carrito = $this->obtener();
        foreach ($carrito as $taller) {
            if ($taller['id'] === $tallerId) {
                return true;
            }
        }
        return false;
    }
}

