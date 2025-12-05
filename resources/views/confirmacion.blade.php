@extends('layouts.app')

@section('title', 'Confirmar Inscripciones - Talleres Cut Tonalá')

@section('content')
    <style>
        .confirmacion-container {
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .confirmacion-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 3px solid #667eea;
            padding-bottom: 1rem;
        }
        
        .confirmacion-header h2 {
            color: #333;
            font-size: 2rem;
            margin: 0 0 0.5rem 0;
        }
        
        .confirmacion-header p {
            color: #666;
            font-size: 1rem;
        }
        
        .resumen-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .resumen-section h3 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        
        .taller-resumen {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: 6px;
            margin-bottom: 0.75rem;
            border-left: 3px solid #667eea;
        }
        
        .taller-resumen:last-child {
            margin-bottom: 0;
        }
        
        .taller-resumen-imagen {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 1rem;
            background: #e9ecef;
        }
        
        .taller-resumen-info {
            flex: 1;
        }
        
        .taller-resumen-info h4 {
            color: #333;
            margin: 0 0 0.25rem 0;
            font-size: 1rem;
        }
        
        .taller-resumen-info p {
            color: #666;
            margin: 0;
            font-size: 0.85rem;
        }
        
        .total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .total-section h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.2rem;
        }
        
        .total-count {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }
        
        .acciones-section {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-volver {
            background: #6c757d;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
            display: inline-block;
        }
        
        .btn-volver:hover {
            background: #5a6268;
        }
        
        .btn-confirmar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-confirmar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
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
        
        .advertencia {
            background: #fff3cd;
            color: #856404;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid #ffc107;
            margin-bottom: 1.5rem;
        }
    </style>

    <div class="confirmacion-container">
        <div class="confirmacion-header">
            <h2> Confirmar Inscripciones</h2>
            <p>Revisa los talleres antes de confirmar tu inscripción</p>
        </div>
        
        @if(session('error'))
            <div class="alert alert-error">
                 {{ session('error') }}
            </div>
        @endif
        
        <div class="advertencia">
             <strong>Atención:</strong> Una vez confirmes, se crearán las inscripciones para los talleres seleccionados.
        </div>
        
        <div class="resumen-section">
            <h3> Talleres seleccionados ({{ $total }})</h3>
            
            @foreach($talleres as $taller)
                <div class="taller-resumen">
                    <div style="width: 60px; height: 60px; overflow: hidden; border-radius: 6px; margin-right: 1rem; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                        @if(isset($taller['foto_url']) && $taller['foto_url'])
                            <img src="{{ $taller['foto_url'] }}" 
                                 alt="{{ $taller['nombre'] }}"
                                 class="taller-resumen-imagen"
                                 onerror="this.style.display='none'">
                        @elseif(isset($taller['foto']) && $taller['foto'])
                            @php
                                $apiBaseUrl = env('API_URL', 'http://cut-tonala-api.test');
                            @endphp
                            <img src="{{ $apiBaseUrl }}/uploads/talleres/{{ $taller['foto'] }}" 
                                 alt="{{ $taller['nombre'] }}"
                                 class="taller-resumen-imagen"
                                 onerror="this.style.display='none'">
                        @else
                            <span style="color: #adb5bd; font-size: 1.5rem;">📚</span>
                        @endif
                    </div>
                    
                    <div class="taller-resumen-info">
                        <h4>{{ $taller['nombre'] }}</h4>
                        @if(isset($taller['categoria_nombre']))
                            <p>Categoría: {{ $taller['categoria_nombre'] }}</p>
                        @endif
                        @if(isset($taller['profesor_nombre']))
                            <p>Profesor: {{ $taller['profesor_nombre'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="total-section">
            <h3>Total de talleres a inscribir</h3>
            <p class="total-count">{{ $total }} {{ $total == 1 ? 'taller' : 'talleres' }}</p>
        </div>
        
        <form action="{{ route('confirmar.post') }}" method="POST">
            @csrf
            
            <div class="acciones-section">
                <a href="{{ route('carrito') }}" class="btn-volver">← Volver al Carrito</a>
                <button type="submit" class="btn-confirmar" onclick="return confirm('¿Estás seguro de confirmar estas inscripciones?')">
                     Confirmar Inscripciones
                </button>
            </div>
        </form>
    </div>
@endsection

