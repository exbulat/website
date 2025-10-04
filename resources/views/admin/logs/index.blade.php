@extends('layouts.app')

@section('title', 'Логи активности')

@section('styles')
<style>
    /* Стили для модального окна */
    .modal-backdrop {
        display: none !important;
    }
    
    .modal {
        background: none !important;
    }
    /* Стили для пагинации */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pagination li {
        margin: 0 2px;
    }
    
    .pagination li a,
    .pagination li span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        color: #6c757d;
        background-color: #fff;
        border: 1px solid #dee2e6;
    }
    
    .pagination li.active span {
        color: #fff;
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .pagination li.disabled span {
        color: #adb5bd;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    .pagination li a:hover {
        color: #4e73df;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    /* Специальные стили для стрелок пагинации */
    .pagination li:first-child a,
    .pagination li:first-child span,
    .pagination li:last-child a,
    .pagination li:last-child span {
        font-size: 20px;
        font-weight: bold;
        background-color: #f8f9fa;
    }
    
    .pagination li:first-child a,
    .pagination li:first-child span {
        color: #007bff;
    }
    
    .pagination li:last-child a,
    .pagination li:last-child span {
        color: #007bff;
    }
    
    .pagination li:first-child a:hover,
    .pagination li:last-child a:hover {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Функция для очистки модальных эффектов
        function cleanupModalEffects() {
            // Удаляем бэкдроп и отменяем блюр
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
        
        // Добавляем обработчик для модального окна очистки логов
        const clearLogsButton = document.querySelector('[data-bs-target="#clearLogsModal"]');
        if (clearLogsButton) {
            clearLogsButton.addEventListener('click', function(e) {
                e.preventDefault();
                const modal = document.getElementById('clearLogsModal');
                
                // Создаем новый экземпляр Bootstrap Modal
                const modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
                
                // Убираем блокировку и блюр
                setTimeout(function() {
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.style.display = 'none';
                    }
                }, 10);
                
                // Добавляем обработчик для закрытия модального окна после отправки формы
                const form = modal.querySelector('form');
                form.addEventListener('submit', function() {
                    modalInstance.hide();
                    cleanupModalEffects();
                });
                
                // Добавляем обработчик для кнопки отмены
                const cancelButton = modal.querySelector('.cancel-modal');
                cancelButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    modalInstance.hide();
                    setTimeout(function() {
                        cleanupModalEffects();
                    }, 100);
                });
                
                // Добавляем обработчик для крестика закрытия
                const closeButton = modal.querySelector('.btn-close');
                closeButton.addEventListener('click', function() {
                    modalInstance.hide();
                    cleanupModalEffects();
                });
                
                // Добавляем обработчик для клика вне модального окна
                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        modalInstance.hide();
                        cleanupModalEffects();
                    }
                });
            });
        }
    });
</script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary me-3">
                        <i class="fas fa-arrow-left me-1"></i> Назад в админ-панель
                    </a>
                </div>
                <div>
                    <a href="{{ route('admin.logs.export', request()->all()) }}" class="btn btn-success">
                        <i class="fas fa-file-export me-1"></i> Экспорт в CSV
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                        <i class="fas fa-trash me-1"></i> Очистить логи
                    </button>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-1"></i> Фильтры
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.logs.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">Пользователь</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Все пользователи</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="action" class="form-label">Действие</label>
                            <select name="action" id="action" class="form-select">
                                <option value="">Все действия</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ (new \App\Models\ActivityLog(['action' => $action]))->action_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="entity_type" class="form-label">Тип сущности</label>
                            <select name="entity_type" id="entity_type" class="form-select">
                                <option value="">Все типы</option>
                                @foreach($entityTypes as $type)
                                    <option value="{{ $type }}" {{ request('entity_type') == $type ? 'selected' : '' }}>
                                        {{ (new \App\Models\ActivityLog(['entity_type' => $type]))->entity_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="search" class="form-label">Поиск</label>
                            <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Поиск по описанию">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Дата от</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Дата до</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i> Применить фильтры
                            </button>
                            <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Сбросить
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <h1 class="my-4">Логи активности</h1>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i> Список логов активности
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Пользователь</th>
                                    <th>Действие</th>
                                    <th>Сущность</th>
                                    <th>Описание</th>
                                    <th>IP-адрес</th>
                                    <th>Дата и время</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            @if($log->user)
                                                {{ $log->user->name }}
                                            @else
                                                <span class="text-muted">Система</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $log->action == 'create' ? 'success' : ($log->action == 'update' ? 'primary' : ($log->action == 'delete' ? 'danger' : 'secondary')) }}">
                                                {{ $log->action_name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->entity_type)
                                                {{ $log->entity_name }} #{{ $log->entity_id }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($log->description, 50) }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('admin.logs.show', $log) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.logs.destroy', $log) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот лог?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">Логи активности не найдены</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <nav>
                            <ul class="pagination">
                                {{-- Предыдущая страница --}}
                                @if ($logs->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $logs->previousPageUrl() }}" rel="prev"><i class="fas fa-chevron-left"></i></a>
                                    </li>
                                @endif

                                {{-- Нумерация страниц --}}
                                @foreach ($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                                    @if ($page == $logs->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Следующая страница --}}
                                @if ($logs->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $logs->nextPageUrl() }}" rel="next"><i class="fas fa-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для подтверждения очистки логов -->
<div class="modal" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearLogsModalLabel">Подтверждение очистки логов</h5>
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Вы уверены, что хотите удалить все логи активности? Это действие нельзя отменить.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-modal" data-bs-dismiss="modal">Отмена</button>
                <form action="{{ route('admin.logs.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить все логи</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
