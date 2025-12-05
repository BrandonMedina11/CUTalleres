@extends('layouts.app')

@section('title', 'Lista de Talleres')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-list-ul"></i> Lista de Talleres
    </h2>
    <span class="badge bg-primary">{{ is_array($talleres) ? count($talleres) : 0 }} talleres</span>
</div>

@if(is_array($talleres) && count($talleres) > 0)
    <div class="row">
        @foreach($talleres as $taller)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card taller-card h-100">
                @if(isset($taller['foto_url']) && $taller['foto_url'])
                    <img src="{{ $taller['foto_url'] }}" class="card-img-top taller-image" alt="{{ $taller['nombre'] }}">
                @else
                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $taller['nombre'] }}</h5>
                    <p class="card-text text-muted">
                        @php
                            $desc = $taller['descripcion'] ?? 'Sin descripción';
                            echo mb_strlen($desc) > 100 ? mb_substr($desc, 0, 100) . '...' : $desc;
                        @endphp
                    </p>
                    <div class="mb-2">
                        @if(isset($taller['categoria_nombre']))
                            <span class="badge bg-info">
                                <i class="bi bi-tag"></i> {{ $taller['categoria_nombre'] }}
                            </span>
                        @endif
                        @if(isset($taller['profesor_nombre']))
                            <span class="badge bg-success">
                                <i class="bi bi-person"></i> {{ $taller['profesor_nombre'] }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('talleres.show', $taller['id']) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-eye"></i> Ver Detalles
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i> No hay talleres disponibles en este momento.
    </div>
@endif
@endsection

