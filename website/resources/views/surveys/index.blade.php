@extends('layouts.app')

@section('title', 'Мои опросы')

@section('content')
<style>
    .card-body {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .card-description {
        flex-grow: 1;
        margin-bottom: 1rem;
    }
    .dropdown-menu {
        max-width: none !important;
        width: auto !important;
    }
    .dropdown-item {
        white-space: nowrap;
        padding: 0.5rem 1rem;
    }
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4 animate-on-scroll">
                <h1>Мои опросы</h1>
                <a href="{{ route('surveys.create') }}" class="btn btn-primary btn-pulse">
                    <i class="fas fa-plus me-2 animated-icon"></i>Создать опрос
                </a>
            </div>

            @if($surveys->isEmpty())
                <div class="card shadow-sm animate-on-scroll">
                    <div class="card-body text-center p-5">
                        <div class="mb-4 animate-on-scroll">
                            <i class="fas fa-poll fa-4x text-muted animated-icon"></i>
                        </div>
                        <h3 class="animate-on-scroll">У вас пока нет опросов</h3>
                        <p class="text-muted animate-on-scroll">Создайте свой первый опрос, чтобы начать собирать ответы</p>
                        <a href="{{ route('surveys.create') }}" class="btn btn-primary btn-pulse animate-on-scroll">
                            <i class="fas fa-plus me-2 animated-icon"></i>Создать опрос
                        </a>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($surveys as $survey)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm card-animated" style="min-height: 300px;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="card-title">{{ $survey->title }}</h5>
                                        <span class="badge {{ $survey->isActive() ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $survey->isActive() ? 'Активен' : 'Неактивен' }}
                                        </span>
                                    </div>
                                    <div class="card-description">
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($survey->description, 100) }}
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center text-muted mb-3">
                                        <i class="fas fa-question-circle me-2"></i>
                                        <span>{{ $survey->questions->count() }} вопросов</span>
                                        <i class="fas fa-users ms-3 me-2"></i>
                                        <span>{{ $survey->answers->groupBy('session_id')->count() }} ответов</span>
                                    </div>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Создан: {{ $survey->created_at->format('d.m.Y') }}
                                            </small>
                                            <div class="dropdown position-static">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Действия
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow" style="position: absolute !important; z-index: 1050 !important; max-height: none !important; overflow: visible !important; bottom: auto !important; top: auto !important;">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('surveys.show', $survey) }}">
                                                        <i class="fas fa-eye me-2"></i>Просмотр
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('surveys.edit', $survey) }}">
                                                        <i class="fas fa-edit me-2"></i>Редактировать
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('surveys.results', $survey) }}">
                                                        <i class="fas fa-chart-bar me-2"></i>Результаты
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('surveys.questions.create', $survey) }}">
                                                        <i class="fas fa-plus me-2"></i>Добавить вопрос
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('surveys.archive.store', $survey) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите архивировать этот опрос?')">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="dropdown-item text-primary">
                                                            <i class="fas fa-archive me-2"></i>Архивировать
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('surveys.destroy', $survey) }}" method="POST" onsubmit="return confirm('ВНИМАНИЕ! Опрос будет удален полностью без возможности восстановления. Продолжить?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash-alt me-2"></i>Удалить полностью
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="text-muted small">Поделиться:</span>
                                        <x-share-buttons 
                                            :url="route('surveys.take', $survey->code)" 
                                            :title="'Опрос: ' . $survey->title" 
                                            :description="$survey->description ?? 'Пройдите этот опрос и поделитесь своим мнением!'" 
                                            class="share-buttons-sm" 
                                        />
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('surveys.take', $survey->code) }}" class="btn btn-outline-primary" target="_blank">
                                            <i class="fas fa-external-link-alt me-2"></i>Открыть опрос
                                        </a>
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
    </div>
</div>
@endsection
