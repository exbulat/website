@extends('admin.layouts.app')

@section('title', 'Панель управления')

@section('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .table-card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .table-card .card-header {
        background-color: #4e73df;
        color: white;
        font-weight: bold;
    }
    
    .table-responsive {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .activity-chart {
        height: 350px;
    }
</style>
@endsection

@section('content')
<!-- Заголовок страницы -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Панель управления</h1>
    <span class="text-muted">{{ date('d.m.Y, H:i') }}</span>
</div>

<!-- Статистика -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 h-100 stat-card primary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Пользователи</div>
                        <div class="stat-number">{{ $totalUsers }}</div>
                        <div class="stat-label">Всего на сайте</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2 text-success small">
                    <i class="fas fa-arrow-up"></i> {{ $newUsers }} новых за неделю
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 h-100 stat-card success">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Опросы</div>
                        <div class="stat-number">{{ $totalSurveys }}</div>
                        <div class="stat-label">Активных на сайте</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-poll fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2 text-success small">
                    <i class="fas fa-arrow-up"></i> {{ $newSurveys }} новых за неделю
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 h-100 stat-card warning">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ответы</div>
                        <div class="stat-number">{{ $totalResponses }}</div>
                        <div class="stat-label">Всего ответов</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2 text-success small">
                    <i class="fas fa-chart-line"></i> Активность растет
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 h-100 stat-card info">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Типы опросов</div>
                        <div class="stat-number">{{ count($surveyTypes) }}</div>
                        <div class="stat-label">Различных типов</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2 small">
                    <span class="text-primary">
                        <i class="fas fa-circle fa-sm"></i> Самый популярный: текстовый
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Графики активности -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Активность за последние 30 дней</h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">Экспорт PDF</a>
                        <a class="dropdown-item" href="#">Экспорт PNG</a>
                        <a class="dropdown-item" href="#">Подробная статистика</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="activity-chart">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Распределение типов вопросов</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="questionTypesChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Выбор одного
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Множественный выбор
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Текстовый
                    </span>
                    <span>
                        <i class="fas fa-circle text-warning"></i> Шкала
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Таблицы с информацией -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4 table-card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Топ активных пользователей</h6>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-users"></i> Все пользователи
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Имя</th>
                                <th scope="col">Email</th>
                                <th scope="col">Опросов</th>
                                <th scope="col">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topUsers as $key => $user)
                                <tr>
                                    <th scope="row">{{ $key + 1 }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->survey_count }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Пользователей не найдено</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4 table-card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Популярные опросы</h6>
                <a href="{{ route('admin.surveys') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-poll"></i> Все опросы
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Название</th>
                                <th scope="col">Прохождений</th>
                                <th scope="col">Вопросов</th>
                                <th scope="col">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topSurveys as $key => $survey)
                                <tr>
                                    <th scope="row">{{ $key + 1 }}</th>
                                    <td>{{ $survey->title }}</td>
                                    <td><span class="badge bg-primary">{{ $survey->completed_count }}</span></td>
                                    <td><span class="badge bg-info">{{ $survey->questions_count }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.surveys.show', $survey->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Опросов не найдено</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // График активности
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($activityData['labels']) !!},
                datasets: [{
                    label: 'Количество ответов',
                    data: {!! json_encode($activityData['data']) !!},
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10
                    }
                }
            }
        });
        
        // График типов вопросов
        const typeLabels = Object.keys({!! json_encode($surveyTypes) !!});
        const typeData = Object.values({!! json_encode($surveyTypes) !!});
        
        // Цвета для типов вопросов
        const typeColors = [
            '#4e73df', // primary
            '#1cc88a', // success
            '#36b9cc', // info
            '#f6c23e'  // warning
        ];
        
        const typeCtx = document.getElementById('questionTypesChart');
        const typesChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: typeLabels,
                datasets: [{
                    data: typeData,
                    backgroundColor: typeColors,
                    hoverBackgroundColor: typeColors.map(color => color.replace(')', ', 0.8)')),
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    }
                }
            }
        });
    });
</script>
@endsection
