@extends('layouts.app')

@section('title', 'Carrito de Inscripciones - Talleres Cut Tonalá')

@section('content')
    <style>
        .carrito-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .carrito-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 3px solid #667eea;
            padding-bottom: 1rem;
        }
        
        .carrito-header h2 {
            color: #333;
            font-size: 2rem;
            margin: 0;
        }
        
        .carrito-count {
            background: #667eea;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .taller-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .taller-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .taller-imagen {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 1.5rem;
            background: #e9ecef;
        }
        
        .taller-info {
            flex: 1;
        }
        
        .taller-info h3 {
            color: #333;
            margin: 0 0 0.5rem 0;
            font-size: 1.2rem;
        }
        
        .taller-info p {
            color: #666;
            margin: 0.25rem 0;
            font-size: 0.9rem;
        }
        
        .taller-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn-eliminar {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 0.9rem;
        }
        
        .btn-eliminar:hover {
            background: #c82333;
        }
        
        .carrito-footer {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .total-info {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .total-count {
            color: #667eea;
        }
        
        .carrito-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn-secundario {
            background: #6c757d;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .btn-secundario:hover {
            background: #5a6268;
        }
        
        .no-items {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
    </style>

    <div class="carrito-container">
        <div class="carrito-header">
            <h2>🛒 Carrito de Inscripciones</h2>
            <span class="carrito-count">{{ $total }} {{ $total == 1 ? 'taller' : 'talleres' }}</span>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                ⚠️ {{ session('error') }}
            </div>
        @endif
        
        @if(empty($talleres))
            <div class="no-items">
                <h3> Tu carrito está vacío</h3>
                <p>Agrega talleres desde el catálogo para comenzar tus inscripciones.</p>
                <a href="{{ route('catalogo') }}" class="btn" style="margin-top: 1rem;">Ver Catálogo de Talleres</a>
            </div>
        @else
            <div>
                @foreach($talleres as $taller)
                    <div class="taller-item">
                        <div style="width: 100px; height: 100px; overflow: hidden; border-radius: 8px; margin-right: 1.5rem; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                            @if(isset($taller['foto_url']) && $taller['foto_url'])
                                <img src="{{ $taller['foto_url'] }}" 
                                     alt="{{ $taller['nombre'] }}"
                                     class="taller-imagen"
                                     onerror="this.style.display='none'">
                            @elseif(isset($taller['foto']) && $taller['foto'])
                                @php
                                    $apiBaseUrl = env('API_URL', 'http://cut-tonala-api.test');
                                @endphp
                                <img src="{{ $apiBaseUrl }}/uploads/talleres/{{ $taller['foto'] }}" 
                                     alt="{{ $taller['nombre'] }}"
                                     class="taller-imagen"
                                     onerror="this.style.display='none'">
                            @else
                                <span style="color: #adb5bd; font-size: 2rem;">📚</span>
                            @endif
                        </div>
                        
                        <div class="taller-info">
                            <h3>{{ $taller['nombre'] }}</h3>
                            @if(isset($taller['categoria_nombre']))
                                <p><strong>Categoría:</strong> {{ $taller['categoria_nombre'] }}</p>
                            @endif
                            @if(isset($taller['profesor_nombre']))
                                <p><strong>Profesor:</strong> {{ $taller['profesor_nombre'] }}</p>
                            @endif
                            @if(isset($taller['descripcion']) && !empty($taller['descripcion']))
                                @php
                                    $descripcion = strlen($taller['descripcion']) > 100 ? substr($taller['descripcion'], 0, 100) . '...' : $taller['descripcion'];
                                @endphp
                                <p style="margin-top: 0.5rem; color: #555;">{{ $descripcion }}</p>
                            @endif
                        </div>
                        
                        <div class="taller-actions">
                            <form action="{{ route('carrito.eliminar', $taller['id']) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este taller del carrito?')">
                                     Eliminar taller
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="carrito-footer">
                <div class="total-info">
                    Total: <span class="total-count">{{ $total }}</span> {{ $total == 1 ? 'taller seleccionado' : 'talleres seleccionados' }}
                </div>
                
                <div class="carrito-actions">
                    <form action="{{ route('carrito.limpiar') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-secundario" onclick="return confirm('¿Estás seguro de limpiar todo el carrito?')">
                            Limpiar Carrito
                        </button>
                    </form>
                    
                    @if(session('token'))
                        <a href="{{ route('confirmar') }}" class="btn">Confirmar Inscripciones →</a>
                    @else
                        <a href="{{ route('login') }}" class="btn">Iniciar Sesión para Confirmar</a>
                    @endif
                </div>
            </div>
        @endif
        
        <div style="margin-top: 2rem; text-align: center;">
            <a href="{{ route('catalogo') }}" class="btn-secundario">← Continuar Explorando</a>
        </div>
    </div>
@endsection

