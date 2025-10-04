@extends('layouts.app')

@section('title', $survey->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ $survey->title }}</h1>
                <div class="d-flex">
                    <a href="{{ route('surveys.edit', $survey) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-edit me-2"></i>Редактировать
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Ещё
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 220px;">
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

            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Информация об опросе</h5>
                            @if($survey->description)
                                <p class="card-text">{{ $survey->description }}</p>
                            @endif
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Статус:</span>
                                                <span class="badge {{ $survey->isActive() ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $survey->isActive() ? 'Активен' : 'Неактивен' }}
                                                </span>
                                            </div>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Видимость:</span>
                                                <span>{{ $survey->is_public ? 'Публичный' : 'Приватный' }}</span>
                                            </div>
                                        </li>
                                        @if(!$survey->is_public && $survey->access_code)
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Код доступа:</span>
                                                <span class="d-flex align-items-center">
                                                    <span id="access-code-value">{{ $survey->access_code }}</span>
                                                    <button class="btn btn-sm btn-link p-0 ms-2" onclick="copyAccessCode()" title="Копировать код доступа">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </li>
                                        @endif
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Создан:</span>
                                                <span>{{ $survey->created_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Дата начала:</span>
                                                <span>{{ $survey->start_at ? $survey->start_at->format('d.m.Y H:i') : 'Не указана' }}</span>
                                            </div>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Дата окончания:</span>
                                                <span>{{ $survey->end_at ? $survey->end_at->format('d.m.Y H:i') : 'Не указана' }}</span>
                                            </div>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <span>Ограничение времени:</span>
                                                <span>{{ $survey->time_limit ? $survey->time_limit . ' сек.' : 'Нет' }}</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <h5 class="mb-0">Вопросы ({{ $questions->count() }})</h5>
                            <a href="{{ route('surveys.questions.create', $survey) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-2"></i>Добавить вопрос
                            </a>
                        </div>
                        <div class="card-body p-0">
                            @if($questions->isEmpty())
                                <div class="text-center p-5">
                                    <div class="mb-3">
                                        <i class="fas fa-question-circle fa-3x text-muted"></i>
                                    </div>
                                    <h5>Нет вопросов</h5>
                                    <p class="text-muted">Добавьте вопросы, чтобы начать собирать ответы</p>
                                    <a href="{{ route('surveys.questions.create', $survey) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Добавить вопрос
                                    </a>
                                </div>
                            @else
                                <ul class="list-group list-group-flush" id="questions-list">
                                    @foreach($questions as $question)
                                        <li class="list-group-item" data-id="{{ $question->id }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $question->title }}</h6>
                                                    <div class="text-muted small">
                                                        <span class="badge bg-light text-dark me-2">{{ App\Models\Question::getTypes()[$question->type] }}</span>
                                                        @if($question->is_required)
                                                            <span class="badge bg-danger">Обязательный</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <a href="{{ route('surveys.questions.edit', [$survey, $question]) }}" class="btn btn-sm btn-outline-secondary me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('surveys.questions.destroy', [$survey, $question]) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить этот вопрос?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Поделиться опросом</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="survey-link" class="form-label">Ссылка на опрос</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="survey-link" value="{{ route('surveys.take', $survey->code) }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('survey-link')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="text-center mb-3">
                                <div class="qr-code-container p-3 border rounded mb-2" style="background-color: white; display: inline-block;">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $qrCodeUrl }}" alt="QR-код" style="display: block; width: 150px; height: 150px;">
                                </div>
                                <div>
                                    <a href="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ $qrCodeUrl }}" class="btn btn-sm btn-outline-secondary" download="qrcode-{{ $survey->code }}.png">
                                        <i class="fas fa-download me-2"></i>Скачать QR-код
                                    </a>
                                </div>
                            </div>
                            

                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('surveys.take', $survey->code) }}" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>Открыть опрос
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Управление результатами</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('surveys.results', $survey) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-chart-bar me-2"></i>Просмотр результатов
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        element.select();
        document.execCommand('copy');
        
        // Показать уведомление
        const button = element.nextElementSibling;
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    }
    
    function copyAccessCode() {
        const codeElement = document.getElementById('access-code-value');
        const text = codeElement.innerText;
        
        // Создаем временный элемент для копирования
        const tempElement = document.createElement('textarea');
        tempElement.value = text;
        document.body.appendChild(tempElement);
        tempElement.select();
        document.execCommand('copy');
        document.body.removeChild(tempElement);
        
        // Показываем уведомление
        const button = codeElement.nextElementSibling;
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    }
</script>
@endsection
