<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Talleres Cut Tonalá')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa;
        }
        
        nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        nav .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        nav h1 {
            font-size: 1.5rem;
        }
        
        nav .nav-links a {
            color: #fff;
            text-decoration: none;
            margin-left: 1.5rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        nav .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        nav .nav-links form {
            display: inline;
            margin-left: 1.5rem;
        }
        
        nav .nav-links button[type="submit"] {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 1rem;
        }
        
        nav .nav-links button[type="submit"]:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
            font-weight: 500;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .loading {
            text-align: center;
            padding: 3rem;
            font-size: 1.2rem;
            color: #667eea;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid #dc3545;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-content">
            <h1>🔧 Talleres Cut Tonalá</h1>
            <div class="nav-links">
                <a href="{{ route('home') }}">Inicio</a>
                <a href="{{ route('catalogo') }}">Catálogo</a>
                @if(session('token'))
                    <a href="{{ route('perfil') }}">Mi Perfil</a>
                    <a href="{{ route('mis-pedidos') }}">Mis Pedidos</a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #fff; cursor: pointer; padding: 0.5rem 1rem; border-radius: 4px; transition: background 0.3s;">Cerrar Sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Iniciar Sesión</a>
                    <a href="{{ route('registro') }}">Registrarse</a>
                @endif
            </div>
        </div>
    </nav>
    
    <div class="container">
        @yield('content')
    </div>
</body>
</html>

