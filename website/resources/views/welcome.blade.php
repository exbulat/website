@extends('layouts.app')

@section('title', 'Главная')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="display-4 fw-bold mb-4">Создавайте интерактивные опросы</h1>
                        <p class="lead mb-4">Наш сервис позволяет легко создавать и проводить опросы, тесты и голосования с мгновенным отображением результатов.</p>
                        
                        @guest
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 me-md-2">Регистрация</a>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4">Вход</a>
                            </div>
                        @else
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="{{ route('surveys.index') }}" class="btn btn-primary btn-lg px-4 me-md-2">Мои опросы</a>
                                <a href="{{ route('surveys.create') }}" class="btn btn-success btn-lg px-4">Создать опрос</a>
                            </div>
                        @endguest
                    </div>
                    <div class="col-md-6 d-none d-md-block">
                        <img src="https://s3.amazonaws.com/cms.ipressroom.com/173/files/20209/5f8f0e412cfac252ec86cccb_In-person+vote/In-person+vote_11c780bd-4fe0-4a84-8a5b-07a0992ef3cb-prv.jpg" alt="Опросы" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mb-3">
                            <i class="fas fa-pencil-alt fa-2x p-3"></i>
                        </div>
                        <h3>Простое создание</h3>
                        <p>Интуитивно понятный интерфейс для создания опросов с различными типами вопросов.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-success bg-gradient text-white rounded-3 mb-3">
                            <i class="fas fa-chart-pie fa-2x p-3"></i>
                        </div>
                        <h3>Визуализация</h3>
                        <p>Наглядные графики и диаграммы для анализа результатов в режиме реального времени.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-info bg-gradient text-white rounded-3 mb-3">
                            <i class="fas fa-share-alt fa-2x p-3"></i>
                        </div>
                        <h3>Удобный доступ</h3>
                        <p>Уникальные ссылки и QR-коды для быстрого доступа к вашим опросам.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .feature-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
        border-radius: 0.75rem;
    }
    
    .bg-gradient {
        background-image: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.1));
    }
</style>
@endsection
