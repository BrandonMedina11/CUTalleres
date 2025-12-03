@extends('layouts.app')

@section('title', 'Bienvenidos - Talleres Cut Tonalá')

@section('content')
    <style>
        .hero {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .hero h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }
    </style>

    <div class="hero">
        <h1>🎉 Bienvenidos a Talleres Cut Tonalá</h1>
        <p>Exploren nuestro catálogo de productos y servicios con la mejor calidad.</p>
        <a href="{{ route('catalogo') }}" class="btn">🔧 Ver Catálogo</a>
    </div>
@endsection