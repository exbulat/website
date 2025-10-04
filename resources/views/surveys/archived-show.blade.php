@extends('layouts.app')

@section('title', 'Результаты архивированного опроса')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.css">
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Результаты архивированного опроса</h1>
        <div class="d-flex gap-2">
            <form action="{{ route('surveys.archive.destroy', $archivedSurvey->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить этот архивированный опрос?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash-alt me-1"></i>Удалить
                </button>
            </form>
            <a href="{{ route('surveys.archive.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Вернуться к архиву
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body position-relative">
                    <span class="archive-badge">
                        <i class="fas fa-archive me-1"></i>Архивирован
                    </span>
                    <h2>{{ $archivedSurvey->title }}</h2>
                    @if($archivedSurvey->description)
                        <p class="lead">{{ $archivedSurvey->description }}</p>
                    @endif
                    <div class="d-flex flex-wrap mt-3">
                        <div class="me-4 mb-2">
                            <small class="text-muted d-block">Создан</small>
                            <span><i class="fas fa-calendar-plus me-1"></i>{{ $archivedSurvey->created_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="me-4 mb-2">
                            <small class="text-muted d-block">Архивирован</small>
                            <span><i class="fas fa-archive me-1"></i>{{ $archivedSurvey->archived_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="me-4 mb-2">
                            <small class="text-muted d-block">Вопросов</small>
                            <span><i class="fas fa-question-circle me-1"></i>{{ $questions->count() }}</span>
                        </div>
                        <div class="me-4 mb-2">
                            <small class="text-muted d-block">Ответов</small>
                            <span><i class="fas fa-users me-1"></i>{{ $totalResponses }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm stats-card primary mb-3">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="response-count">{{ $totalResponses }}</div>
                                </div>
                                <div class="col">
                                    <div class="response-label">Всего ответов</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mb-4">
        <h3 class="h4 mb-3">Результаты по вопросам</h3>
        
        @foreach($questions as $question)
            <div class="card shadow-sm result-card">
                <div class="card-header bg-white">
                    <h4 class="h5 mb-0">{{ $question->content }}</h4>
                </div>
                <div class="card-body">
                    @if($question->type === 'single_choice' || $question->type === 'multiple_choice')
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="d-flex justify-content-end mb-2">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Тип графика">
                                        <button type="button" class="btn btn-outline-primary active" data-chart-type="bar" data-question-id="{{ $question->id }}">
                                            <i class="fas fa-chart-bar"></i> 
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-chart-type="line" data-question-id="{{ $question->id }}">
                                            <i class="fas fa-chart-line"></i> 
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-chart-type="pie" data-question-id="{{ $question->id }}">
                                            <i class="fas fa-chart-pie"></i> 
                                        </button>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="chart-{{ $question->id }}"></canvas>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <h5 class="h6 mb-3">Распределение ответов</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Вариант</th>
                                                <th class="text-end">Кол-во</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($questionData[$question->id]['options'] as $option => $count)
                                                <tr>
                                                    <td>{{ $option }}</td>
                                                    <td class="text-end">{{ $count }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <th>Всего</th>
                                                <th class="text-end">{{ $questionData[$question->id]['total'] }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @elseif($question->type === 'scale')
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Для шкалы отображаем только столбчатый график без кнопок переключения -->
                                <div class="chart-container">
                                    <canvas id="chart-{{ $question->id }}"></canvas>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <h5 class="h6 mb-3">Статистика</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th>Диапазон шкалы</th>
                                                <td class="text-end">
                                                    {{ isset($question->options['min']) ? $question->options['min'] : 1 }} - 
                                                    {{ isset($question->options['max']) ? $question->options['max'] : 10 }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Среднее значение</th>
                                                <td class="text-end">{{ $questionData[$question->id]['average'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Всего ответов</th>
                                                <td class="text-end">{{ $questionData[$question->id]['total'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <h5 class="h6 mb-3 mt-4">Распределение</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Оценка</th>
                                                <th class="text-end">Кол-во</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($questionData[$question->id]['distribution'] as $value => $count)
                                                <tr>
                                                    <td>{{ $value }}</td>
                                                    <td class="text-end">{{ $count }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @elseif($question->type === 'text')
                        <h5 class="h6 mb-3">Текстовые ответы ({{ isset($questionData[$question->id]['answers']) ? count($questionData[$question->id]['answers']) : 0 }})</h5>
                        
                        @if(!isset($questionData[$question->id]['answers']) || empty($questionData[$question->id]['answers']))
                            <div class="alert alert-info">
                                Нет ответов на этот вопрос.
                            </div>
                        @else
                            <div class="list-group">
                                @foreach($questionData[$question->id]['answers'] as $answer)
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <p class="mb-1">{{ $answer['text'] }}</p>
                                            <small class="text-muted">{{ $answer['date'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        Chart.register(ChartDataLabels);
        
        // Цвета для графиков
        const colors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
            '#6f42c1', '#fd7e14', '#20c9a6', '#5a5c69', '#858796'
        ];
        
        // Объект для хранения экземпляров графиков
        const chartInstances = {};
        
        // Объект для хранения данных графиков
        const chartDatasets = {};
        
        // Функция для создания графика
        function createChart(questionId, type) {
            const ctx = document.getElementById('chart-' + questionId).getContext('2d');
            
            // Если график уже существует, уничтожаем его
            if (chartInstances[questionId]) {
                chartInstances[questionId].destroy();
            }
            
            // Получаем данные для графика
            const chartData = chartDatasets[questionId];
            
            // Настройки в зависимости от типа графика
            let options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: type === 'pie',
                        position: 'right'
                    },
                    datalabels: {
                        color: type === 'pie' ? '#fff' : '#000',
                        font: {
                            weight: 'bold'
                        },
                        formatter: function(value, context) {
                            return value > 0 ? value : '';
                        }
                    }
                }
            };
            
            // Добавляем специфичные настройки для разных типов графиков
            if (type !== 'pie') {
                options.scales = {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                };
            }
            
            // Создаем новый график
            chartInstances[questionId] = new Chart(ctx, {
                type: type,
                data: chartData,
                options: options
            });
        }
        
        // Обработчик клика по кнопкам выбора типа графика
        document.querySelectorAll('[data-chart-type]').forEach(button => {
            button.addEventListener('click', function() {
                const questionId = this.getAttribute('data-question-id');
                const chartType = this.getAttribute('data-chart-type');
                
                // Убираем активный класс у всех кнопок для этого вопроса
                document.querySelectorAll(`[data-question-id="${questionId}"]`).forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Добавляем активный класс текущей кнопке
                this.classList.add('active');
                
                // Создаем график нужного типа
                createChart(questionId, chartType);
            });
        });
        
        // Графики для вопросов
        @foreach($questions as $question)
            @if($question->type === 'single_choice' || $question->type === 'multiple_choice')
                // Данные для графика вопроса {{ $question->id }}
                const chartData{{ $question->id }} = {
                    labels: {!! json_encode(array_map('strval', array_keys($questionData[$question->id]['options']))) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($questionData[$question->id]['options'])) !!},
                        backgroundColor: colors.slice(0, {!! count($questionData[$question->id]['options']) !!}),
                        borderWidth: 0
                    }]
                };
                
                // Сохраняем данные в общий объект
                chartDatasets[{{ $question->id }}] = chartData{{ $question->id }};
                
                // Создаем график по умолчанию (столбчатый)
                const ctx{{ $question->id }} = document.getElementById('chart-{{ $question->id }}').getContext('2d');
                chartInstances[{{ $question->id }}] = new Chart(ctx{{ $question->id }}, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_map('strval', array_keys($questionData[$question->id]['options']))) !!},
                        datasets: [{
                            data: {!! json_encode(array_values($questionData[$question->id]['options'])) !!},
                            backgroundColor: colors.slice(0, {!! count($questionData[$question->id]['options']) !!}),
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: function(value, context) {
                                    return value > 0 ? value : '';
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        }
                    }
                });
            @elseif($question->type === 'scale')
                // Данные для графика вопроса {{ $question->id }}
                const chartData{{ $question->id }} = {
                    labels: {!! json_encode(array_keys($questionData[$question->id]['distribution'])) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($questionData[$question->id]['distribution'])) !!},
                        backgroundColor: colors.slice(0, 10),
                        borderWidth: 0
                    }]
                };
                
                // Сохраняем данные в общий объект
                chartDatasets[{{ $question->id }}] = chartData{{ $question->id }};
                
                // Создаем график по умолчанию (столбчатый) - для шкалы всегда используем столбчатый график
                const ctx{{ $question->id }} = document.getElementById('chart-{{ $question->id }}').getContext('2d');
                chartInstances[{{ $question->id }}] = new Chart(ctx{{ $question->id }}, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($questionData[$question->id]['distribution'])) !!},
                        datasets: [{
                            data: {!! json_encode(array_values($questionData[$question->id]['distribution'])) !!},
                            backgroundColor: '#4e73df',
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: function(value, context) {
                                    return value > 0 ? value : '';
                                }
                            },
                            title: {
                                display: true,
                                text: 'Распределение ответов по шкале от ' + 
                                      '{{ isset($question->options["min"]) ? $question->options["min"] : 1 }}' + 
                                      ' до ' + 
                                      '{{ isset($question->options["max"]) ? $question->options["max"] : 10 }}',
                                font: {
                                    size: 14
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        }
                    }
                });
            @endif
        @endforeach
    });
</script>
@endsection