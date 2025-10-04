@extends('admin.layouts.app')

@section('title', 'Детали опроса')

@section('styles')
<style>
    .question-card {
        margin-bottom: 1rem;
        border-left: 4px solid #4e73df;
    }
    
    .question-header {
        background-color: #f8f9fc;
        padding: 0.75rem 1.25rem;
        margin-bottom: 0;
        border-bottom: 1px solid #e3e6f0;
        font-weight: 700;
    }
    
    .question-type {
        text-transform: uppercase;
        font-size: 0.7rem;
        background-color: #4e73df;
        color: white;
        padding: 0.2rem 0.6rem;
        border-radius: 10rem;
    }
    
    .question-options {
        list-style-type: none;
        padding-left: 0;
    }
    
    .question-options li {
        margin-bottom: 0.5rem;
        padding: 0.5rem;
        background-color: #f8f9fc;
        border-radius: 0.25rem;
    }
    
    .survey-meta-card {
        background-color: #f8f9fc;
        border: none;
    }
    
    .survey-meta-item {
        padding: 0.75rem;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
    }
    
    .survey-meta-item:last-child {
        border-bottom: none;
    }
    
    .survey-meta-label {
        font-weight: 600;
        color: #5a5c69;
    }
    
    .response-data-card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<!-- Заголовок страницы -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Детали опроса</h1>
    <div>
        <a href="{{ route('surveys.take', $survey->code) }}" class="btn btn-success me-2" target="_blank">
            <i class="fas fa-external-link-alt me-2"></i>Открыть опрос
        </a>
        <a href="{{ route('admin.surveys') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Назад к списку
        </a>
    </div>
</div>

<div class="row">
    <!-- Основная информация -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">{{ $survey->title }}</h6>
                <div class="d-flex">
                    <span class="badge {{ $survey->is_active ? 'bg-success' : 'bg-secondary' }} me-2">
                        {{ $survey->is_active ? 'Активный' : 'Неактивный' }}
                    </span>
                    <a href="{{ route('surveys.results', $survey->id) }}" class="btn btn-sm btn-primary" target="_blank">
                        <i class="fas fa-chart-bar me-1"></i>Результаты
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="font-weight-bold">Описание:</h6>
                    <p>{{ $survey->description ?? 'Описание отсутствует' }}</p>
                </div>
                
                <h6 class="font-weight-bold mb-3">Вопросы ({{ $survey->questions->count() }}):</h6>
                
                @forelse($survey->questions as $index => $question)
                    <div class="card question-card mb-3">
                        <h6 class="question-header">
                            <span class="me-2">{{ $index + 1 }}.</span> 
                            {{ $question->title }}
                            <span class="question-type float-end">
                                @switch($question->type)
                                    @case('single_choice')
                                        Выбор одного
                                        @break
                                    @case('multiple_choice')
                                        Множественный выбор
                                        @break
                                    @case('text')
                                        Текстовый
                                        @break
                                    @case('scale')
                                        Шкала
                                        @break
                                    @default
                                        {{ $question->type }}
                                @endswitch
                            </span>
                        </h6>
                        <div class="card-body">
                            @if($question->type === 'single_choice' || $question->type === 'multiple_choice')
                                <ul class="question-options">
                                    @foreach($question->options as $option)
                                        <li>{{ $option }}</li>
                                    @endforeach
                                </ul>
                            @elseif($question->type === 'scale')
                                <div class="d-flex justify-content-between">
                                    <div>Минимум: {{ $question->scale_min ?? '1' }}</div>
                                    <div>Максимум: {{ $question->scale_max ?? '10' }}</div>
                                </div>
                            @elseif($question->type === 'text')
                                <p class="text-muted">Текстовое поле для ввода</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning">
                        Этот опрос не содержит вопросов.
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Статистика ответов -->
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Статистика ответов</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <h2 class="h4 font-weight-bold">{{ $responseCount }}</h2>
                        <p class="text-muted">Всего ответов</p>
                    </div>
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <h2 class="h4 font-weight-bold">{{ $survey->views }}</h2>
                        <p class="text-muted">Просмотров</p>
                    </div>

                </div>
                
                <!-- Здесь можно добавить график с распределением ответов по времени -->
                <div class="chart-container">
                    <canvas id="responseTimeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Мета-информация -->
    <div class="col-lg-4">
        <!-- Информация о создателе -->
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Информация о создателе</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <i class="fas fa-user-circle fa-3x text-gray-300"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-0">{{ $survey->user->name ?? 'Неизвестно' }}</h6>
                        <div class="text-muted">{{ $survey->user->email ?? 'Нет данных' }}</div>
                    </div>
                </div>
                
                @if($survey->user)
                    <a href="{{ route('admin.users.edit', $survey->user->id) }}" class="btn btn-outline-primary btn-sm d-block w-100">
                        <i class="fas fa-user-edit me-2"></i>Просмотреть профиль
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Технические детали -->
        <div class="card shadow-sm mb-4 survey-meta-card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Детали опроса</h6>
            </div>
            <div class="card-body p-0">
                <div class="survey-meta-item">
                    <span class="survey-meta-label">ID опроса:</span>
                    <span>{{ $survey->id }}</span>
                </div>
                <div class="survey-meta-item">
                    <span class="survey-meta-label">Код доступа:</span>
                    <span>{{ $survey->code }}</span>
                </div>
                <div class="survey-meta-item">
                    <span class="survey-meta-label">Создан:</span>
                    <span>{{ $survey->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="survey-meta-item">
                    <span class="survey-meta-label">Последнее обновление:</span>
                    <span>{{ $survey->updated_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="survey-meta-item">
                    <span class="survey-meta-label">Статус:</span>
                    <span class="badge {{ $survey->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $survey->is_active ? 'Активный' : 'Неактивный' }}
                    </span>
                </div>
                <div class="survey-meta-item">
                    <span class="survey-meta-label">Количество вопросов:</span>
                    <span>{{ $survey->questions->count() }}</span>
                </div>
            </div>
        </div>
        
        <!-- Действия -->
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Действия</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form action="{{ route('admin.surveys.toggle', $survey->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn {{ $survey->is_active ? 'btn-warning' : 'btn-success' }} w-100 mb-2">
                            <i class="fas {{ $survey->is_active ? 'fa-ban' : 'fa-check' }} me-2"></i>
                            {{ $survey->is_active ? 'Деактивировать' : 'Активировать' }} опрос
                        </button>
                    </form>
                    
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSurveyModal">
                        <i class="fas fa-trash me-2"></i>Удалить опрос
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="deleteSurveyModal" tabindex="-1" aria-labelledby="deleteSurveyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSurveyModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы действительно хотите удалить опрос "<strong>{{ $survey->title }}</strong>"?</p>
                <p class="text-danger">Это действие нельзя отменить! Все вопросы и ответы будут удалены.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form action="{{ route('admin.surveys.destroy', $survey->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить опрос</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация графика распределения ответов по времени
        const responseTimeCtx = document.getElementById('responseTimeChart').getContext('2d');
        
        // Данные за последние 7 дней (заглушка)
        const labels = [];
        const responseData = [];
        
        // Генерируем тестовые данные для графика
        const today = new Date();
        for (let i = 6; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);
            labels.push(date.getDate() + '.' + (date.getMonth() + 1));
            
            // Рандомные данные для примера
            responseData.push(Math.floor(Math.random() * 10));
        }
        
        const responseTimeChart = new Chart(responseTimeCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Количество ответов',
                    data: responseData,
                    backgroundColor: 'rgba(78, 115, 223, 0.7)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Распределение ответов за последнюю неделю',
                        padding: {
                            top: 10,
                            bottom: 30
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
