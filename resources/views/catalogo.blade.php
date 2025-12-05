@extends('layouts.app')

@section('title', 'Catálogo de Talleres - Talleres Cut Tonalá')

@section('content')
    <style>
        .catalogo-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .catalogo-header h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .producto-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            min-height: 400px;
            display: flex;
            flex-direction: column;
        }

        .producto-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }
        
        .producto-card > * {
            flex-shrink: 0;
        }
        
        .producto-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
            background-color: #f0f0f0;
            display: block;
            min-height: 220px;
        }
        
        .producto-card h3 {
            margin: 0.5rem 0;
            color: #333;
            font-size: 1.2rem;
        }
        
        .producto-card .marca {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .producto-card .precio {
            font-size: 1.4rem;
            color: #28a745;
            font-weight: bold;
            margin: 1rem 0;
        }

        .no-productos {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 12px;
            color: #666;
        }
    </style>

    <div class="catalogo-header">
        <h1>🔧 Catálogo de Talleres</h1>
        <p style="color: #666;">Descubre nuestros talleres, productos y servicios de calidad</p>
    </div>

    @if(session('success'))
        <div class="error-message" style="background: #d4edda; color: #155724; border-left-color: #28a745;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="error-message">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    @if($error)
        <div class="error-message">
            ⚠️ {{ $error }}
            <br><small style="margin-top: 0.5rem; display: block;">Verifica que la API esté corriendo en: <strong>{{ $apiBaseUrl }}</strong></small>
        </div>
    @endif

    @if(empty($productos) && !$error)
        <div class="no-productos">
            <h2> No hay talleres disponibles</h2>
            <p>Vuelve pronto para ver nuestras novedades</p>
        </div>
    @elseif(!empty($productos))
        <div class="productos-grid">
            @foreach($productos as $taller)
                <div class="producto-card">
                    <div style="width: 100%; height: 220px; overflow: hidden; border-radius: 8px; margin-bottom: 1rem; background-color: #f0f0f0;">
                        @if(isset($taller['foto_url']) && $taller['foto_url'])
                            <img src="{{ $taller['foto_url'] }}" 
                                 alt="{{ $taller['nombre'] }}"
                                 style="width: 100%; height: 100%; object-fit: cover; display: block;"
                                 loading="lazy"
                                 onerror="this.src='https://via.placeholder.com/280x220?text=Sin+Imagen'">
                        @elseif(isset($taller['foto']) && $taller['foto'])
                            <img src="{{ $apiBaseUrl }}/uploads/talleres/{{ $taller['foto'] }}" 
                                 alt="{{ $taller['nombre'] }}"
                                 style="width: 100%; height: 100%; object-fit: cover; display: block;"
                                 loading="lazy"
                                 onerror="this.src='https://via.placeholder.com/280x220?text=Sin+Imagen'">
                        @else
                            <img src="https://via.placeholder.com/280x220?text=Sin+Imagen" 
                                 alt="Sin imagen"
                                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        @endif
                    </div>
                    
                    <h3 style="flex-grow: 0;">{{ $taller['nombre'] }}</h3>
                    @if(isset($taller['categoria_nombre']))
                        <p class="marca" style="flex-grow: 0;">{{ $taller['categoria_nombre'] }}</p>
                    @endif
                    @if(isset($taller['profesor_nombre']))
                        <p class="marca" style="font-size: 0.85rem; color: #888; flex-grow: 0;">Profesor: {{ $taller['profesor_nombre'] }}</p>
                    @endif
                    
                    <div style="margin-top: auto; padding-top: 1rem; display: flex; gap: 0.5rem; flex-direction: column;">
                        <a href="{{ route('producto.detalle', $taller['id']) }}" class="btn" style="text-align: center;">
                            ver más detalles
                        </a>
                        @php
                            $estaEnCarrito = in_array($taller['id'], $talleresEnCarrito ?? []);
                        @endphp
                        @if($estaEnCarrito)
                            <a href="{{ route('carrito') }}" class="btn" style="background: #28a745; text-align: center;">
                                ✅ En el carrito
                            </a>
                        @else
                            <form action="{{ route('carrito.agregar') }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="taller_id" value="{{ $taller['id'] }}">
                                <input type="hidden" name="nombre" value="{{ $taller['nombre'] }}">
                                <button type="submit" class="btn" style="width: 100%; background: #667eea; border: none; cursor: pointer;">
                                    🛒 Agregar al carrito
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection