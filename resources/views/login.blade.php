@extends('layouts.app')

@section('title', 'Iniciar Sesión - Talleres Cut Tonalá')

@section('content')
    <style>
        .auth-container {
            max-width: 450px;
            margin: 2rem auto;
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .auth-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
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
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
        }
    </style>

    <div class="auth-container">
        <h2>Iniciar Sesión</h2>
        
        @if(session('error'))
            <div class="alert alert-error">
                ⚠️ {{ session('error') }}
            </div>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            
            <div class="form-group">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" value="{{ old('correo') }}" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-submit">Entrar</button>
        </form>
        
        <div class="register-link">
            ¿No tienes cuenta? <a href="{{ route('registro') }}">Regístrate aquí</a>
        </div>
    </div>
@endsection


