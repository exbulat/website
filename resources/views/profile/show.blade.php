@extends('layouts.app')

@section('title', 'Профиль пользователя')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="card shadow-sm mb-4 animate-on-scroll">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Профиль пользователя</h5>
                    <div>
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Редактировать
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4 mb-md-0">
                            @if ($user->avatar)
                                <img src="{{ asset('public/storage/' . $user->avatar) }}" alt="Аватар пользователя" class="img-fluid rounded-circle mb-3 animate-on-scroll" style="max-width: 150px; height: auto;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 animate-on-scroll" style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-4x text-secondary animated-icon"></i>
                                </div>
                            @endif
                            <h5>{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->email }}</p>
                            <a href="{{ route('profile.change-password.form') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-key me-1"></i>Сменить пароль
                            </a>
                        </div>
                        <div class="col-md-9">
                            <h5 class="border-bottom pb-2 mb-3 animate-on-scroll">Статистика</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-primary text-white stats-card animate-on-scroll">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">Создано опросов</h6>
                                                    <h2 class="mb-0">{{ $stats['surveys_created'] }}</h2>
                                                </div>
                                                <i class="fas fa-poll fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-success text-white stats-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">Пройдено опросов</h6>
                                                    <h2 class="mb-0">{{ $stats['surveys_participated'] }}</h2>
                                                </div>
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-info text-white stats-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">Всего <br>ответов</h6>
                                                    <h2 class="mb-0">{{ $stats['total_answers'] }}</h2>
                                                </div>
                                                <i class="fas fa-comment-dots fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Последняя активность</h5>
                            <div class="list-group">
                                @if($user->surveys()->count() > 0)
                                    @foreach($user->surveys()->latest()->take(3)->get() as $survey)
                                        <a href="{{ route('surveys.show', $survey) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $survey->title }}</h6>
                                                <small>{{ $survey->created_at->format('d.m.Y') }}</small>
                                            </div>
                                            <p class="mb-1 text-muted">Создан опрос</p>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <p>У вас пока нет созданных опросов</p>
                                        <a href="{{ route('surveys.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>Создать опрос
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
