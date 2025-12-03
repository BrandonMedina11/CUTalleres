@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-white text-center py-4">
                <h4 class="mb-0">
                    <i class="bi bi-lock-fill text-warning"></i> Registro
                </h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('registro') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre completo</label>
                        <input 
                            type="text" 
                            class="form-control @if(isset($_SESSION['errors'])) is-invalid @endif" 
                            id="nombre" 
                            name="nombre" 
                            value="{{ old('nombre') }}"
                            placeholder="Ingresa tu nombre completo"
                            required
                            autofocus
                        >
                        @if(isset($_SESSION['errors']))
                            <div class="invalid-feedback">
                                @foreach($_SESSION['errors'] as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo electrónico</label>
                        <input 
                            type="email" 
                            class="form-control @if(isset($_SESSION['errors'])) is-invalid @endif" 
                            id="correo" 
                            name="correo" 
                            value="{{ old('correo') }}"
                            placeholder="tu@correo.com"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input 
                            type="password" 
                            class="form-control @if(isset($_SESSION['errors'])) is-invalid @endif" 
                            id="contrasena" 
                            name="contrasena" 
                            placeholder="Mínimo 6 caracteres"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="contrasena_confirmacion" class="form-label">Confirmar contraseña</label>
                        <input 
                            type="password" 
                            class="form-control @if(isset($_SESSION['errors'])) is-invalid @endif" 
                            id="contrasena_confirmacion" 
                            name="contrasena_confirmacion" 
                            placeholder="Repite tu contraseña"
                            required
                        >
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-person-plus"></i> Registrarse
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <p class="mb-0">
                            ¿Ya tienes una cuenta? 
                            <a href="{{ route('home') }}">Inicia sesión</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

