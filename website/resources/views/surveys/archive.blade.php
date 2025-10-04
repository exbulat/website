@extends('layouts.app')

@section('title', 'Архив опросов')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Архив опросов</h1>
        <div>
            <a href="{{ route('surveys.history') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Вернуться к истории
            </a>
        </div>
    </div>
    
    @if($archivedSurveys->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>У вас нет архивированных опросов.
        </div>
    @else
        <div class="row">
            @foreach($archivedSurveys as $survey)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-truncate" title="{{ $survey->title }}">
                                {{ $survey->title }}
                            </h5>
                            <span class="badge bg-secondary">
                                <i class="fas fa-archive me-1"></i>Архив
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="card-text text-muted small mb-2">
                                <i class="fas fa-calendar-alt me-1"></i>Архивирован: {{ $survey->archived_at ? $survey->archived_at->format('d.m.Y H:i') : 'Дата не указана' }}
                            </p>
                            
                            @if($survey->description)
                                <p class="card-text">{{ Str::limit($survey->description, 100) }}</p>
                            @else
                                <p class="card-text text-muted fst-italic">Нет описания</p>
                            @endif
                            
                            <div class="d-flex justify-content-between mt-3">
                                <span class="badge bg-secondary">
                                    <i class="fas fa-question-circle me-1"></i>{{ $survey->questions()->count() }} вопр.
                                </span>
                                <span class="badge bg-info">
                                    <i class="fas fa-users me-1"></i>{{ $survey->answers()->count() }} отв.
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2">
                                <a href="{{ route('surveys.archive.show', $survey->id) }}" class="btn btn-outline-primary flex-grow-1">
                                    <i class="fas fa-chart-bar me-2"></i>Просмотреть результаты
                                </a>
                                <form action="{{ route('surveys.archive.destroy', $survey->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить этот архивированный опрос?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $archivedSurveys->links() }}
        </div>
    @endif
</div>
@endsection
