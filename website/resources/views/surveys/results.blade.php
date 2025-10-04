@extends('layouts.app')

@section('title', 'Результаты опроса: ' . $survey->title)

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.css">
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h1 class="h3 mb-3 mb-md-0">Результаты опроса: {{ $survey->title }}</h1>
        
        <div class="d-flex flex-wrap gap-2 action-buttons">
            <x-share-buttons 
                :url="route('surveys.results', $survey)" 
                :title="'Результаты опроса: ' . $survey->title" 
                :description="'Посмотрите результаты опроса: ' . $survey->title" 
                class="share-buttons-responsive" 
            />
            
            <div class="btn-group export-dropdown">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-2"></i><span class="export-text">Экспорт</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <a class="dropdown-item" href="{{ route('surveys.export', ['survey' => $survey, 'format' => 'csv']) }}">
                            <i class="fas fa-file-csv me-2"></i>CSV
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('surveys.export', ['survey' => $survey, 'format' => 'pdf']) }}">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" id="export-png">
                            <i class="fas fa-file-image me-2"></i>PNG
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    

        
    <div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stats-card warning animate-on-scroll">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Последний ответ</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lastResponseDate }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- График активности -->
    <div class="card shadow-sm mb-4 animate-on-scroll">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Активность по дням</h6>
        </div>
        <div class="card-body">
            <div class="chart-container chart-animated">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Результаты по вопросам -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Результаты по вопросам</h2>
        
        <div class="category-filter-buttons mb-0">
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn {{ request('category') === null ? 'btn-primary' : 'btn-outline-primary' }} category-btn" data-category="all">Все</button>
                @foreach($questions->pluck('category')->unique()->filter() as $category)
                    <button type="button" class="btn {{ request('category') === $category ? 'btn-primary' : 'btn-outline-primary' }} category-btn" data-category="{{ $category }}">{{ $category }}</button>
                @endforeach
                <button type="button" class="btn {{ request('category') === 'uncategorized' ? 'btn-primary' : 'btn-outline-primary' }} category-btn" data-category="uncategorized">Без категории</button>
            </div>
        </div>
    </div>
    
    @foreach($questions as $question)
        @php
            $showQuestion = true;
            if (request('category') === 'uncategorized' && !empty($question->category)) {
                $showQuestion = false;
            } elseif (request('category') && request('category') !== 'uncategorized' && $question->category !== request('category')) {
                $showQuestion = false;
            }
        @endphp
        
        <div class="card shadow-sm result-card {{ !$showQuestion ? 'd-none' : '' }}" data-category="{{ $question->category ?? 'uncategorized' }}">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">{{ $loop->iteration }}. {{ $question->title }}</h6>
            </div>
            <div class="card-body">
                @if($question->type === 'single_choice' || $question->type === 'multiple_choice')
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="d-flex justify-content-end mb-2">
                                <div class="btn-group btn-group-sm chart-type-selector" data-question-id="{{ $question->id }}">
                                    <button type="button" class="btn btn-outline-primary active" data-chart-type="bar" title="Столбчатая диаграмма">
                                        <i class="fas fa-chart-bar"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" data-chart-type="pie" title="Круговая диаграмма">
                                        <i class="fas fa-chart-pie"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" data-chart-type="line" title="Линейная диаграмма">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="chart-container chart-animated">
                                <canvas id="chart-{{ $question->id }}"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Вариант</th>
                                            <th>Количество</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($questionResults[$question->id]['options'] as $option => $count)
                                            <tr>
                                                <td>{{ $option }}</td>
                                                <td>{{ $count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif($question->type === 'scale')
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="d-flex justify-content-end mb-2">
                                <div class="btn-group btn-group-sm chart-type-selector" data-question-id="{{ $question->id }}">
                                    <button type="button" class="btn btn-outline-primary active" data-chart-type="bar" title="Столбчатая диаграмма">
                                        <i class="fas fa-chart-bar"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" data-chart-type="pie" title="Круговая диаграмма">
                                        <i class="fas fa-chart-pie"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" data-chart-type="line" title="Линейная диаграмма">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="chart-container chart-animated">
                                <canvas id="chart-{{ $question->id }}"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="mb-3 text-center">
                                        <span class="response-count">{{ $questionResults[$question->id]['average'] }}</span>
                                        <div class="response-label">Средняя оценка</div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="text-center">
                                            <span class="d-block fw-bold">{{ $questionResults[$question->id]['min'] }}</span>
                                            <small class="text-muted">Минимум</small>
                                        </div>
                                        <div class="text-center">
                                            <span class="d-block fw-bold">{{ $questionResults[$question->id]['max'] }}</span>
                                            <small class="text-muted">Максимум</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($question->type === 'text')
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ответ</th>
                                    <th>Дата</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionResults[$question->id]['answers'] as $index => $answer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $answer['text'] }}</td>
                                        <td>{{ $answer['date'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
    
    <!-- Кнопки навигации -->
    <div class="d-flex justify-content-between mt-4 mb-5">
        <a href="{{ route('surveys.show', $survey->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Вернуться к опросу
        </a>
        
        @if(auth()->check() && auth()->id() === $survey->user_id)
            <a href="{{ route('surveys.edit', $survey->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-edit me-2"></i>Редактировать опрос
            </a>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
<script>
    // Фильтрация по категориям с помощью кнопок
    document.addEventListener('DOMContentLoaded', function() {
        const categoryButtons = document.querySelectorAll('.category-btn');
        const questionCards = document.querySelectorAll('.result-card');
        const categorySelect = document.getElementById('category');
        const filterForm = document.getElementById('filter-form');
        
        // Обработчик кликов по кнопкам категорий
        categoryButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Предотвращаем переход по ссылке
                const category = this.getAttribute('data-category');
                
                // Изменяем активную кнопку
                categoryButtons.forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                
                // Фильтруем вопросы на стороне клиента без перезагрузки страницы
                questionCards.forEach(card => {
                    if (category === 'all') {
                        card.classList.remove('d-none');
                    } else if (category === 'uncategorized') {
                        if (!card.getAttribute('data-category') || card.getAttribute('data-category') === 'uncategorized') {
                            card.classList.remove('d-none');
                        } else {
                            card.classList.add('d-none');
                        }
                    } else {
                        if (card.getAttribute('data-category') === category) {
                            card.classList.remove('d-none');
                        } else {
                            card.classList.add('d-none');
                        }
                    }
                });
                
                // Обновляем значение выпадающего списка
                if (categorySelect) {
                    if (category === 'all') {
                        categorySelect.value = '';
                    } else {
                        categorySelect.value = category;
                    }
                }
            });
        });
        
        // Проверка наличия формы фильтрации
        if (filterForm) {
            // Добавляем обработчик отправки формы
            filterForm.addEventListener('submit', function(e) {
                // Проверяем, есть ли в URL параметр code
                const currentUrl = window.location.href;
                const hasCode = currentUrl.includes('/s/');
                
                if (hasCode) {
                    // Если это публичный URL с кодом, используем его
                    const codeMatch = currentUrl.match(/\/s\/([^\/]+)/);
                    if (codeMatch && codeMatch[1]) {
                        const code = codeMatch[1];
                        this.action = '/s/' + code + '/results';
                    }
                }
            });
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Регистрация плагина datalabels
        Chart.register(ChartDataLabels);
        
        // Настройки цветов для графиков
        const colors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
            '#5a5c69', '#6f42c1', '#fd7e14', '#20c9a6', '#858796'
        ];
        
        // График активности
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($activityData['labels']) !!},
                datasets: [{
                    label: 'Количество ответов',
                    data: {!! json_encode($activityData['data']) !!},
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: '#4e73df',
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#4e73df',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    datalabels: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });
        
        // Графики для вопросов
        @foreach($questions as $question)
            @if($question->type === 'single_choice' || $question->type === 'multiple_choice')
                const questionId{{ $question->id }} = {{ $question->id }};
                const labels{{ $question->id }} = {!! json_encode(array_keys($questionResults[$question->id]['options'])) !!};
                const data{{ $question->id }} = {!! json_encode(array_values($questionResults[$question->id]['options'])) !!};
                const backgroundColors{{ $question->id }} = colors.slice(0, {!! count($questionResults[$question->id]['options']) !!});
                
                // Функция для создания диаграммы с выбранным типом
                function createChart{{ $question->id }}(type) {
                    // Если диаграмма уже существует, уничтожаем её
                    if (window.chart{{ $question->id }}) {
                        window.chart{{ $question->id }}.destroy();
                    }
                    
                    const ctx{{ $question->id }} = document.getElementById('chart-{{ $question->id }}').getContext('2d');
                    
                    // Настройки в зависимости от типа диаграммы
                    let options = {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: type === 'pie', // Показываем легенду только для круговой диаграммы
                                position: 'bottom'
                            },
                            datalabels: {
                                color: type === 'pie' ? '#fff' : '#000',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: function(value, context) {
                                    if (value <= 0) return '';
                                    return type === 'pie' ? value : value;
                                }
                            }
                        }
                    };
                    
                    // Добавляем оси для bar и line диаграмм
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
                    
                    // Настройка данных в зависимости от типа диаграммы
                    let chartData = {
                        labels: labels{{ $question->id }},
                        datasets: [{
                            data: data{{ $question->id }},
                            backgroundColor: backgroundColors{{ $question->id }},
                            borderWidth: type === 'line' ? 2 : 0
                        }]
                    };
                    
                    // Для линейной диаграммы добавляем дополнительные настройки
                    if (type === 'line') {
                        chartData.datasets[0].borderColor = '#4e73df';
                        chartData.datasets[0].pointBackgroundColor = '#4e73df';
                        chartData.datasets[0].pointBorderColor = '#fff';
                        chartData.datasets[0].tension = 0.1;
                        chartData.datasets[0].fill = false;
                    }
                    
                    // Создаем диаграмму
                    window.chart{{ $question->id }} = new Chart(ctx{{ $question->id }}, {
                        type: type,
                        data: chartData,
                        options: options
                    });
                    
                    // Сохраняем выбранный тип в localStorage
                    localStorage.setItem('chartType_' + questionId{{ $question->id }}, type);
                }
                
                // Получаем сохраненный тип диаграммы или используем 'bar' по умолчанию
                const savedChartType{{ $question->id }} = localStorage.getItem('chartType_' + questionId{{ $question->id }}) || 'bar';
                
                // Создаем диаграмму с сохраненным или дефолтным типом
                createChart{{ $question->id }}(savedChartType{{ $question->id }});
                
                // Обновляем активную кнопку в селекторе типа диаграммы
                document.querySelectorAll('.chart-type-selector[data-question-id="{{ $question->id }}"] button').forEach(button => {
                    if (button.dataset.chartType === savedChartType{{ $question->id }}) {
                        button.classList.add('active');
                    } else {
                        button.classList.remove('active');
                    }
                });
                
                // Добавляем обработчики событий для кнопок выбора типа диаграммы
                document.querySelectorAll('.chart-type-selector[data-question-id="{{ $question->id }}"] button').forEach(button => {
                    button.addEventListener('click', function() {
                        // Удаляем класс active у всех кнопок
                        document.querySelectorAll('.chart-type-selector[data-question-id="{{ $question->id }}"] button').forEach(btn => {
                            btn.classList.remove('active');
                        });
                        
                        // Добавляем класс active текущей кнопке
                        this.classList.add('active');
                        
                        // Создаем диаграмму выбранного типа
                        createChart{{ $question->id }}(this.dataset.chartType);
                    });
                });
            @elseif($question->type === 'scale')
                const questionId{{ $question->id }} = {{ $question->id }};
                const labels{{ $question->id }} = {!! json_encode(array_keys($questionResults[$question->id]['distribution'])) !!};
                const data{{ $question->id }} = {!! json_encode(array_values($questionResults[$question->id]['distribution'])) !!};
                
                // Функция для создания диаграммы с выбранным типом
                function createChart{{ $question->id }}(type) {
                    // Если диаграмма уже существует, уничтожаем её
                    if (window.chart{{ $question->id }}) {
                        window.chart{{ $question->id }}.destroy();
                    }
                    
                    const ctx{{ $question->id }} = document.getElementById('chart-{{ $question->id }}').getContext('2d');
                    
                    // Настройки в зависимости от типа диаграммы
                    let options = {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: type === 'pie', // Показываем легенду только для круговой диаграммы
                                position: 'bottom'
                            },
                            datalabels: {
                                color: type === 'pie' ? '#fff' : '#000',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: function(value, context) {
                                    if (value <= 0) return '';
                                    return type === 'pie' ? value : value;
                                }
                            }
                        }
                    };
                    
                    // Добавляем оси для bar и line диаграмм
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
                    
                    // Создаем массив цветов для шкалы
                    const scaleColors = Array(labels{{ $question->id }}.length).fill('#4e73df');
                    
                    // Настройка данных в зависимости от типа диаграммы
                    let chartData = {
                        labels: labels{{ $question->id }},
                        datasets: [{
                            data: data{{ $question->id }},
                            backgroundColor: scaleColors,
                            borderWidth: type === 'line' ? 2 : 0
                        }]
                    };
                    
                    // Для линейной диаграммы добавляем дополнительные настройки
                    if (type === 'line') {
                        chartData.datasets[0].borderColor = '#4e73df';
                        chartData.datasets[0].pointBackgroundColor = '#4e73df';
                        chartData.datasets[0].pointBorderColor = '#fff';
                        chartData.datasets[0].tension = 0.1;
                        chartData.datasets[0].fill = false;
                    }
                    
                    // Создаем диаграмму
                    window.chart{{ $question->id }} = new Chart(ctx{{ $question->id }}, {
                        type: type,
                        data: chartData,
                        options: options
                    });
                    
                    // Сохраняем выбранный тип в localStorage
                    localStorage.setItem('chartType_' + questionId{{ $question->id }}, type);
                }
                
                // Получаем сохраненный тип диаграммы или используем 'bar' по умолчанию
                const savedChartType{{ $question->id }} = localStorage.getItem('chartType_' + questionId{{ $question->id }}) || 'bar';
                
                // Создаем диаграмму с сохраненным или дефолтным типом
                createChart{{ $question->id }}(savedChartType{{ $question->id }});
            @endif
        @endforeach
        
        // Экспорт в PNG
        document.getElementById('export-png').addEventListener('click', function(e) {
            e.preventDefault();
            
            const resultsContainer = document.querySelector('.container-fluid');
            
            // Создаем временный контейнер для скриншота
            const tempContainer = document.createElement('div');
            tempContainer.style.position = 'absolute';
            tempContainer.style.left = '-9999px';
            tempContainer.style.width = '1200px';
            tempContainer.appendChild(resultsContainer.cloneNode(true));
            document.body.appendChild(tempContainer);
            
            // Используем html2canvas для создания изображения
            html2canvas(tempContainer, {
                scale: 1,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                // Создаем ссылку для скачивания
                const link = document.createElement('a');
                link.download = 'результаты_опроса_{{ $survey->code }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                // Удаляем временный контейнер
                document.body.removeChild(tempContainer);
            });
        });
    });
</script>
@endsection
