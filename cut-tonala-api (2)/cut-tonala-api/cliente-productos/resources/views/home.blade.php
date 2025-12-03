@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="hero-section">
    <div class="container">
        <h1 class="display-4">Bienvenidos a la Tienda Online</h1>
        <p class="lead">Exploren nuestro catálogo de productos.</p>
        <a href="{{ route('catalogo') }}" class="btn btn-light btn-lg mt-3">
            <i class="bi bi-grid"></i> Ver Catálogo
        </a>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2>Nuestros Productos</h2>
            <p class="text-muted">Descubre nuestra amplia variedad de productos de calidad</p>
            <a href="{{ route('catalogo') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-arrow-right"></i> Explorar Catálogo
            </a>
        </div>
    </div>
</div>
@endsection


