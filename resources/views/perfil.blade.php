@extends('layouts.app')

@section('title', 'Mi Perfil - Talleres Cut Tonalá')

@section('content')
    <style>
        .perfil-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .perfil-container h2 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            border-bottom: 3px solid #667eea;
            padding-bottom: 0.5rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 1.5rem;
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
            font-size: 1.2rem;
            font-weight: 500;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
    </style>

    <div class="perfil-container">
        <h2>👤 Mi Perfil</h2>
        
        @if($error)
            <div class="alert alert-error">
                ⚠️ {{ $error }}
            </div>
        @endif
        
        @if($usuario)
            <div class="info-grid">
                <div class="info-item">
                    <strong>Nombre</strong>
                    <div class="value">{{ $usuario['nombre'] ?? 'N/A' }}</div>
                </div>
                
                <div class="info-item">
                    <strong>Correo electrónico</strong>
                    <div class="value">{{ $usuario['correo'] ?? 'N/A' }}</div>
                </div>
                
                @if(isset($usuario['rol']))
                <div class="info-item">
                    <strong>Rol</strong>
                    <div class="value">{{ $usuario['rol'] }}</div>
                </div>
                @endif
                
                @if(isset($usuario['id']))
                <div class="info-item">
                    <strong>ID de Usuario</strong>
                    <div class="value">#{{ $usuario['id'] }}</div>
                </div>
                @endif
            </div>
        @else
            <p style="color: #666; text-align: center; padding: 2rem;">
                No se pudo cargar la información del perfil.
            </p>
        @endif
        
        <div style="margin-top: 2rem; text-align: center;">
            <a href="{{ route('mis-pedidos') }}" class="btn">Ver Mis Pedidos</a>
            <a href="{{ route('catalogo') }}" class="btn" style="margin-left: 1rem;">Ver Catálogo</a>
        </div>
    </div>
@endsection


