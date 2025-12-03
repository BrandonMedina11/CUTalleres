@extends('layouts.app')

@section('title', isset($producto) ? $producto['nombre'] : 'Detalle de Producto')

@section('content')
@if($cargando)
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3">Cargando detalle...</p>
    </div>
@endif

@if($error)
    <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ $error }}
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('catalogo') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Volver al Catálogo
        </a>
    </div>
@endif

@if($producto && !$cargando)
    <div class="row">
        <div class="col-md-6">
            <div class="imagenes-detalle">
                @if(isset($producto['imagen1']))
                    <img src="http://localhost:3000/uploads/{{ $producto['imagen1'] }}" 
                         alt="Imagen 1" 
                         class="img-fluid mb-3">
                @endif
                @if(isset($producto['imagen2']))
                    <img src="http://localhost:3000/uploads/{{ $producto['imagen2'] }}" 
                         alt="Imagen 2" 
                         class="img-fluid mb-3">
                @endif
                @if(isset($producto['imagen3']))
                    <img src="http://localhost:3000/uploads/{{ $producto['imagen3'] }}" 
                         alt="Imagen 3" 
                         class="img-fluid mb-3">
                @endif
                @if(!isset($producto['imagen1']) && !isset($producto['imagen2']) && !isset($producto['imagen3']))
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px; border-radius: 8px;">
                        <i class="bi bi-image" style="font-size: 4rem; color: #ccc;"></i>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <h1 class="mb-4">{{ $producto['nombre'] ?? 'Sin nombre' }}</h1>
            
            <div class="mb-3">
                <p><strong>Marca:</strong> {{ $producto['marca'] ?? 'N/A' }}</p>
            </div>
            
            <div class="mb-3">
                <p><strong>Descripción:</strong></p>
                <p>{{ $producto['descripcion'] ?? 'Sin descripción disponible' }}</p>
            </div>
            
            <div class="mb-3">
                <p class="h3 text-primary">
                    <strong>Precio:</strong> ${{ number_format($producto['precio'] ?? 0, 2) }}
                </p>
            </div>
            
            <div class="mb-3">
                <p><strong>Existencia:</strong> 
                    @if(isset($producto['existencia']))
                        @if($producto['existencia'] > 0)
                            <span class="badge bg-success">{{ $producto['existencia'] }} unidades disponibles</span>
                        @else
                            <span class="badge bg-danger">Agotado</span>
                        @endif
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </p>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('catalogo') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Catálogo
                </a>
            </div>
        </div>
    </div>
@endif
@endsection


