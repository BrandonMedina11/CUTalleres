@extends('layouts.app')

@section('title', 'Mis Pedidos - Talleres Cut Tonalá')

@section('content')
    <style>
        .pedidos-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .pedidos-container h2 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            border-bottom: 3px solid #667eea;
            padding-bottom: 0.5rem;
        }
        
        .pedido-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .pedido-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .pedido-id {
            font-size: 1.2rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .pedido-fecha {
            color: #666;
            font-size: 0.9rem;
        }
        
        .pedido-total {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
            margin-top: 0.5rem;
        }
        
        .no-pedidos {
            text-align: center;
            padding: 3rem;
            color: #666;
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

    <div class="pedidos-container">
        <h2> Mis Talleres Guardados</h2>
        
        @if($error)
            <div class="alert alert-error">
                ⚠️ {{ $error }}
            </div>
        @endif
        
        @if(empty($pedidos))
            <div class="no-pedidos">
                <h3> No tienes Talleres registrados</h3>
                <p>Cuando guardes un Talleres, aparecerá aquí.</p>
                <a href="{{ route('catalogo') }}" class="btn" style="margin-top: 1rem;">Ver Catálogo de Talleres</a>
            </div>
        @else
            <div>
                @foreach($pedidos as $pedido)
                    <div class="pedido-card">
                        <div class="pedido-header">
                            <div>
                                <span class="pedido-id">Pedido #{{ $pedido['id'] ?? 'N/A' }}</span>
                                @if(isset($pedido['fecha']))
                                    <div class="pedido-fecha">📅 {{ $pedido['fecha'] }}</div>
                                @endif
                            </div>
                            @if(isset($pedido['estado']))
                                <span style="padding: 0.5rem 1rem; background: #28a745; color: white; border-radius: 20px; font-size: 0.85rem;">
                                    {{ $pedido['estado'] }}
                                </span>
                            @endif
                        </div>
                        
                        @if(isset($pedido['total']))
                            <div class="pedido-total">
                                Total: ${{ number_format($pedido['total'], 2) }} USD
                            </div>
                        @endif
                        
                        @if(isset($pedido['descripcion']))
                            <p style="margin-top: 0.5rem; color: #666;">{{ $pedido['descripcion'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
        
        <div style="margin-top: 2rem; text-align: center;">
            <a href="{{ route('perfil') }}" class="btn">Volver al Perfil</a>
            <a href="{{ route('catalogo') }}" class="btn" style="margin-left: 1rem;">Ver Catálogo</a>
        </div>
    </div>
@endsection


