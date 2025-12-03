@extends('layouts.app')

@section('title', 'Catálogo de Productos')

@section('content')
<h1 class="mb-4"><i class="bi bi-grid"></i> Catálogo de Productos</h1>

@if($cargando)
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3">Cargando productos...</p>
    </div>
@endif

@if($error)
    <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ $error }}
    </div>
@endif

@if(!$cargando && !$error)
    @if(empty($productos))
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No hay productos disponibles en este momento.
        </div>
    @else
        <div class="row">
            @foreach($productos as $producto)
                <div class="col-md-4 mb-4">
                    <div class="card producto-card h-100">
                        @if(isset($producto['imagen1']))
                            <img src="http://localhost:3000/uploads/{{ $producto['imagen1'] }}" 
                                 alt="{{ $producto['nombre'] ?? 'Producto' }}" 
                                 class="producto-image">
                        @else
                            <div class="producto-image bg-light d-flex align-items-center justify-content-center">
                                <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $producto['nombre'] ?? 'Sin nombre' }}</h5>
                            <p class="text-muted mb-2">
                                <strong>Marca:</strong> {{ $producto['marca'] ?? 'N/A' }}
                            </p>
                            <p class="card-text">
                                {{ isset($producto['descripcion']) ? substr($producto['descripcion'], 0, 100) . '...' : 'Sin descripción' }}
                            </p>
                            <div class="mt-auto">
                                <p class="h4 text-primary mb-3">
                                    ${{ number_format($producto['precio'] ?? 0, 2) }}
                                </p>
                                <a href="{{ route('producto.show', ['id' => $producto['id']]) }}" 
                                   class="btn btn-primary w-100">
                                    <i class="bi bi-eye"></i> Ver Detalle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endif
@endsection


