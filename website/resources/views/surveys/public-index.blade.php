@extends('layouts.app')

@section('title', 'Публичные опросы')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-4">Публичные опросы</h1>
            <p class="lead text-muted">Просматривайте и участвуйте в публичных опросах</p>
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
            <i class="fas fa-info-circle me-2"></i>
            @if(request()->routeIs('public.surveys.search'))
                По запросу "{{ request('q') }}" не найдено опросов.
            @else
                В данный момент нет доступных публичных опросов.
            @endif
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
                            </div>
                            @if($survey->end_at && $survey->end_at->isPast())
                                <div class="alert alert-warning py-1 px-2 mb-2 small">
                                    <i class="fas fa-clock me-1"></i>Опрос завершён
                                </div>
                            @elseif($survey->end_at)
                                <div class="alert alert-info py-1 px-2 mb-2 small">
                                    <i class="fas fa-clock me-1"></i>До {{ $survey->end_at->format('d.m.Y') }}
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="{{ route('surveys.take', $survey->code) }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-poll me-1"></i>Пройти опрос
                            </a>
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
@endsection
