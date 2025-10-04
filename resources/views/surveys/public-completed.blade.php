@extends('layouts.app')

@section('title', 'Завершённые опросы')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-4">Завершённые опросы</h1>
            <p class="lead text-muted">Опросы, которые уже завершены и доступны для просмотра результатов</p>
        </div>
        <div class="col-md-4">
            <form action="{{ route('public.surveys.search') }}" method="GET" class="mt-3">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Поиск опросов..." value="{{ request('q') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('public.surveys.index') ? 'active' : '' }}" href="{{ route('public.surveys.index') }}">
                        <i class="fas fa-list me-1"></i>Все опросы
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('public.surveys.popular') ? 'active' : '' }}" href="{{ route('public.surveys.popular') }}">
                        <i class="fas fa-fire me-1"></i>Популярные
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('public.surveys.completed') ? 'active' : '' }}" href="{{ route('public.surveys.completed') }}">
                        <i class="fas fa-check-circle me-1"></i>Завершённые
                    </a>
                </li>
            </ul>
        </div>
    </div>

    @if($surveys->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>В данный момент нет завершённых опросов с доступными результатами.
        </div>
    @else
        <div class="row">
            @foreach($surveys as $survey)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $survey->title }}</h5>
                            <p class="card-text text-muted small">
                                {{ Str::limit($survey->description, 100) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary">{{ $survey->questions_count }} вопросов</span>
                                <span class="badge bg-secondary">{{ $survey->responses_count }} ответов</span>
                            </div>
                            <div class="alert alert-warning py-1 px-2 mb-2 small">
                                <i class="fas fa-clock me-1"></i>Завершён {{ $survey->end_at->format('d.m.Y') }}
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('surveys.view-responses', $survey->code) }}" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="fas fa-list-check me-2"></i>Мои ответы
                                </a>
                                @auth
                                    @if(Auth::id() === $survey->user_id)
                                        <a href="{{ route('surveys.results', $survey) }}" class="btn btn-info btn-sm flex-grow-1">
                                            <i class="fas fa-chart-bar me-2"></i>Общие результаты
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $surveys->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Проверяем все кнопки "Пройти опрос"
        const surveyButtons = document.querySelectorAll('.card-footer a[href*="/surveys/"]');
        
        surveyButtons.forEach(button => {
            const href = button.getAttribute('href');
            const surveyCode = href.split('/').pop();
            
            // Если опрос уже пройден (информация есть в localStorage)
            if (localStorage.getItem('completed_survey_' + surveyCode)) {
                // Меняем текст кнопки и ссылку
                button.innerHTML = '<i class="fas fa-eye me-1"></i>Просмотреть ответы';
                button.href = `/surveys/${surveyCode}/view-responses`;
            }
        });
    });
</script>
@endpush

@endsection
