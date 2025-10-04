@extends('layouts.app')

@section('title', 'Доступ к опросу: ' . $survey->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="mb-0">Доступ к опросу</h3>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-4x text-warning"></i>
                    </div>
                    
                    <h4 class="mb-4">{{ $survey->title }}</h4>
                    
                    <p class="lead mb-4">
                        Этот опрос защищен кодом доступа. Пожалуйста, введите код для продолжения.
                    </p>
                    
                    <form action="{{ route('surveys.take', $survey->code) }}" method="GET">
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="text" name="access_code" class="form-control form-control-lg" placeholder="Введите код доступа" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                            
                            @if(session('error'))
                                <div class="text-danger mt-2">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                    </form>
                    
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-link">
                            <i class="fas fa-arrow-left me-2"></i>Вернуться на главную
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
