@extends('layouts.app')

@section('title', ($producto['nombre'] ?? 'Detalle del Producto') . ' - Talleres Cut Tonalá')

@section('content')
    <style>
        .detalle-container {
            background: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .detalle-container h2 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            border-bottom: 3px solid #667eea;
            padding-bottom: 0.5rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .info-item strong {
            color: #555;
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-item .value {
            color: #333;
            font-size: 1.1rem;
        }
        
        .precio-grande {
            font-size: 2rem;
            color: #28a745;
            font-weight: bold;
        }

        .existencia {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #28a745;
            color: white;
            border-radius: 20px;
            font-weight: bold;
        }

        .existencia.baja {
            background: #ffc107;
        }

        .existencia.agotado {
            background: #dc3545;
        }
        
        .imagenes-detalle {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .imagenes-detalle img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .imagenes-detalle img:hover {
            transform: scale(1.05);
        }
        
        .btn-volver {
            margin-top: 2rem;
        }
    </style>

    <div class="detalle-container">
        @if(session('success'))
            <div class="error-message" style="background: #d4edda; color: #155724; border-left-color: #28a745; margin-bottom: 1rem;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="error-message" style="margin-bottom: 1rem;">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <h2>{{ $producto['nombre'] }}</h2>
        
        <div class="info-grid">
            @if(isset($producto['categoria_nombre']))
            <div class="info-item">
                <strong> Categoría</strong>
                <div class="value">{{ $producto['categoria_nombre'] }}</div>
            </div>
            @endif

            @if(isset($producto['profesor_nombre']))
            <div class="info-item">
                <strong> Profesor</strong>
                <div class="value">{{ $producto['profesor_nombre'] }}</div>
            </div>
            @endif

            @if(isset($producto['id']))
            <div class="info-item">
                <strong> ID</strong>
                <div class="value">#{{ $producto['id'] }}</div>
            </div>
            @endif
        </div>

        <div class="info-item" style="margin-bottom: 2rem;">
            <strong> Descripción</strong>
            <div class="value" style="line-height: 1.8;">{{ $producto['descripcion'] ?? 'Sin descripción disponible' }}</div>
        </div>

        @if(isset($producto['foto_url']) && $producto['foto_url'])
            <h3 style="margin-bottom: 1rem; color: #333;"> Imagen del Taller</h3>
            <div class="imagenes-detalle">
                <img src="{{ $producto['foto_url'] }}" 
                     alt="{{ $producto['nombre'] }}"
                     onerror="this.style.display='none'">
            </div>
        @elseif(isset($producto['foto']) && $producto['foto'])
            <h3 style="margin-bottom: 1rem; color: #333;"> Imagen del Taller</h3>
            <div class="imagenes-detalle">
                <img src="{{ $apiBaseUrl }}/uploads/talleres/{{ $producto['foto'] }}" 
                     alt="{{ $producto['nombre'] }}"
                     onerror="this.style.display='none'">
            </div>
        @endif

        <div style="margin-top: 2rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            @if($estaEnCarrito ?? false)
                <a href="{{ route('carrito') }}" class="btn" style="background: #28a745;">
                    ✅ Ya está en el carrito - Ver carrito
                </a>
            @else
                <form action="{{ route('carrito.agregar') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="taller_id" value="{{ $producto['id'] }}">
                    <input type="hidden" name="nombre" value="{{ $producto['nombre'] ?? '' }}">
                    <button type="submit" class="btn" style="background: #667eea; border: none; cursor: pointer;">
                        🛒 Agregar al carrito de inscripciones
                    </button>
                </form>
            @endif
            <a href="{{ route('catalogo') }}" class="btn" style="background: #6c757d;">← Volver al catálogo</a>
        </div>
    </div>
@endsection

