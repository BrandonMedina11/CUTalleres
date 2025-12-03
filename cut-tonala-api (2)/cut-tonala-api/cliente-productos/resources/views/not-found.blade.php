@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
<div class="text-center py-5">
    <h1 class="display-1">404</h1>
    <h2 class="mb-4">Página no encontrada</h2>
    <p class="lead text-muted mb-4">La página que buscan no existe.</p>
    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
        <i class="bi bi-house"></i> Ir al inicio
    </a>
</div>
@endsection


