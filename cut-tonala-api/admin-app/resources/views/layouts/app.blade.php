<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin App') - CUT Tonalá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
        }
        .taller-card {
            transition: transform 0.2s;
        }
        .taller-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .taller-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
    @yield('styles')
</head>
<body>
    @if(session('usuario') || isset($_SESSION['usuario']))
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('talleres.index') }}">
                <i class="bi bi-building"></i> Admin CUT Tonalá
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('talleres.index') }}">
                            <i class="bi bi-list-ul"></i> Talleres
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="bi bi-person-circle"></i> {{ (session('usuario') ?: ($_SESSION['usuario'] ?? []))['correo'] ?? 'Usuario' }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-white">
                                <i class="bi bi-box-arrow-right"></i> Salir
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endif

    <div class="container mt-4">
        @if(isset($_SESSION['success']))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ $_SESSION['success'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @php unset($_SESSION['success']); @endphp
        @endif

        @if(isset($_SESSION['error']) || isset($_SESSION['errors']))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                @if(isset($_SESSION['error']))
                    {{ $_SESSION['error'] }}
                    @php unset($_SESSION['error']); @endphp
                @elseif(isset($_SESSION['errors']))
                    @if(is_array($_SESSION['errors']))
                        <ul class="mb-0">
                            @foreach($_SESSION['errors'] as $error)
                                <li>{{ is_array($error) ? implode(', ', $error) : $error }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $_SESSION['errors'] }}
                    @endif
                    @php unset($_SESSION['errors']); @endphp
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>

