@extends('layouts.app')

@section('title', 'Página no encontrada - Talleres Cut Tonalá')

@section('content')
    <style>
        .not-found-container {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .not-found-container h1 {
            font-size: 3rem;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .not-found-container p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }
        
        .error-code {
            font-size: 6rem;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 1rem;
        }
    </style>

    <div class="not-found-container">
        <div class="error-code">404</div>
        <h1>Página no encontrada</h1>
        <p>La página que buscan no existe.</p>
        <a href="{{ route('home') }}" class="btn">🏠 Ir al inicio</a>
    </div>
@endsection

