@extends('layouts.app')

@section('title', 'Detalles del Taller')

@section('content')
<div class="mb-4">
    <a href="{{ route('talleres.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver a la Lista
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">
            <i class="bi bi-info-circle"></i> Detalles del Taller
        </h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                @if(isset($taller['foto_url']) && $taller['foto_url'])
                    <img src="{{ $taller['foto_url'] }}" class="img-fluid rounded" alt="{{ $taller['nombre'] }}">
                @else
                    <div class="bg-secondary d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                        <i class="bi bi-image text-white" style="font-size: 5rem;"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <h3>{{ $taller['nombre'] }}</h3>
                <hr>
                <p class="text-muted">{{ $taller['descripcion'] ?? 'Sin descripción' }}</p>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>Información del Taller</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th><i class="bi bi-hash"></i> ID:</th>
                                <td>{{ $taller['id'] }}</td>
                            </tr>
                            @if(isset($taller['categoria_nombre']))
                            <tr>
                                <th><i class="bi bi-tag"></i> Categoría:</th>
                                <td>{{ $taller['categoria_nombre'] }}</td>
                            </tr>
                            @endif
                            @if(isset($taller['profesor_nombre']))
                            <tr>
                                <th><i class="bi bi-person"></i> Profesor:</th>
                                <td>{{ $taller['profesor_nombre'] }}</td>
                            </tr>
                            @endif
                            @if(isset($taller['profesor_email']))
                            <tr>
                                <th><i class="bi bi-envelope"></i> Email Profesor:</th>
                                <td>{{ $taller['profesor_email'] }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

