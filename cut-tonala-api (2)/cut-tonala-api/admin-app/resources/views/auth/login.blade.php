@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="correo" class="form-label">
                            <i class="bi bi-envelope"></i> Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            class="form-control @error('correo') is-invalid @enderror" 
                            id="correo" 
                            name="correo" 
                            value="{{ old('correo') }}"
                            placeholder="usuario@ejemplo.com"
                            required
                            autofocus
                        >
                        @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contrasena" class="form-label">
                            <i class="bi bi-lock"></i> Contraseña
                        </label>
                        <input 
                            type="password" 
                            class="form-control @error('contrasena') is-invalid @enderror" 
                            id="contrasena" 
                            name="contrasena" 
                            placeholder="Ingresa tu contraseña"
                            required
                        >
                        @error('contrasena')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('error')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center text-muted">
                <small>Interfaz Administrativa - CUT Tonalá</small>
            </div>
        </div>
    </div>
</div>
@endsection

