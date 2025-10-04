@extends('admin.layouts.app')

@section('title', 'Управление опросами')

@section('styles')
<style>
    .survey-table {
        background-color: #fff;
    }
    
    .survey-table th {
        font-weight: 600;
    }
    
    .filter-card {
        margin-bottom: 1.5rem;
        background-color: #f8f9fc;
        border: none;
    }
    
    .badge-active {
        background-color: #1cc88a;
        color: white;
    }
    
    .badge-inactive {
        background-color: #858796;
        color: white;
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
        padding: 6px;
        margin: 0 2px;
    }
    
    .table-header {
        background-color: #f8f9fc;
    }
    
    .table-responsive {
        border-radius: 0.35rem;
        overflow: hidden;
    }
    
    .sorting-header {
        cursor: pointer;
        position: relative;
    }
    
    .sorting-header::after {
        content: '\f0dc';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        margin-left: 0.5rem;
        opacity: 0.4;
    }
    
    .sorting-header.asc::after {
        content: '\f0de';
        opacity: 1;
    }
    
    .sorting-header.desc::after {
        content: '\f0dd';
        opacity: 1;
    }
    
    .survey-title {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<!-- Заголовок страницы -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Управление опросами</h1>
</div>

<!-- Фильтры -->
<div class="card shadow-sm filter-card">
    <div class="card-body">
        <form action="{{ route('admin.surveys') }}" method="GET" class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Поиск по названию" name="search" value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <div class="col-md-3 mb-3 mb-md-0">
                <select class="form-select" name="status" onchange="this.form.submit()">
                    <option value="">Все статусы</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Неактивные</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3 mb-md-0">
                <select class="form-select" name="sort" onchange="this.form.submit()">
                    <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Дата создания</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Название</option>
                    <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Просмотры</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <select class="form-select" name="order" onchange="this.form.submit()">
                    <option value="desc" {{ request('order', 'desc') == 'desc' ? 'selected' : '' }}>По убыванию</option>
                    <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>По возрастанию</option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Таблица опросов -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped survey-table mb-0">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="sorting-header {{ request('sort') == 'id' ? (request('order', 'desc') == 'desc' ? 'desc' : 'asc') : '' }}" data-sort="id">#</th>
                        <th scope="col" class="sorting-header {{ request('sort') == 'title' ? (request('order', 'desc') == 'desc' ? 'desc' : 'asc') : '' }}" data-sort="title">Название</th>
                        <th scope="col">Автор</th>
                        <th scope="col">Статус</th>
                        <th scope="col" class="sorting-header {{ request('sort') == 'views' ? (request('order', 'desc') == 'desc' ? 'desc' : 'asc') : '' }}" data-sort="views">Просмотры</th>
                        <th scope="col" class="sorting-header {{ request('sort') == 'created_at' ? (request('order', 'desc') == 'desc' ? 'desc' : 'asc') : '' }}" data-sort="created_at">Создан</th>
                        <th scope="col" class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surveys as $survey)
                        <tr>
                            <th scope="row">{{ $survey->id }}</th>
                            <td class="survey-title" title="{{ $survey->title }}">{{ $survey->title }}</td>
                            <td>{{ $survey->user->name ?? 'Нет данных' }}</td>
                            <td>
                                @if($survey->is_active)
                                    <span class="badge rounded-pill badge-active">Активный</span>
                                @else
                                    <span class="badge rounded-pill badge-inactive">Неактивный</span>
                                @endif
                            </td>
                            <td>{{ $survey->views }}</td>
                            <td>{{ $survey->created_at->format('d.m.Y H:i') }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.surveys.show', $survey->id) }}" class="btn btn-info action-btn" data-bs-toggle="tooltip" title="Просмотр">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.surveys.toggle', $survey->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn {{ $survey->is_active ? 'btn-warning' : 'btn-success' }} action-btn" data-bs-toggle="tooltip" title="{{ $survey->is_active ? 'Деактивировать' : 'Активировать' }}">
                                            <i class="fas {{ $survey->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form>
                                    
                                    <button type="button" class="btn btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $survey->id }}" title="Удалить">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Опросов не найдено</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Пагинация -->
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Показано {{ $surveys->firstItem() ?? 0 }} - {{ $surveys->lastItem() ?? 0 }} из {{ $surveys->total() }} опросов
            </div>
            <div>
                {{ $surveys->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Модальные окна для подтверждения удаления -->
@foreach($surveys as $survey)
<div class="modal" id="deleteModal{{ $survey->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $survey->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $survey->id }}">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы действительно хотите удалить опрос "<strong>{{ $survey->title }}</strong>"?</p>
                <p class="text-danger">Это действие невозможно отменить! Все вопросы и ответы будут удалены.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-modal">Отмена</button>
                <form action="{{ route('admin.surveys.destroy', $survey->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Обработчик для сортировки по заголовкам таблицы
        const sortingHeaders = document.querySelectorAll('.sorting-header');
        sortingHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const sort = this.getAttribute('data-sort');
                const currentSort = new URLSearchParams(window.location.search).get('sort') || 'created_at';
                const currentOrder = new URLSearchParams(window.location.search).get('order') || 'desc';
                
                // Определяем новый порядок сортировки
                const newOrder = (sort === currentSort && currentOrder === 'desc') ? 'asc' : 'desc';
                
                // Создаем новый URL с параметрами сортировки
                const url = new URL(window.location.href);
                url.searchParams.set('sort', sort);
                url.searchParams.set('order', newOrder);
                
                // Переходим по новому URL
                window.location.href = url.toString();
            });
        });
        
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
        
        // Добавляем обработчик для модальных окон
        const deleteButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('data-bs-target');
                const modal = document.querySelector(target);
                
                // Создаем новый экземпляр Bootstrap Modal
                const modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
                
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
        });
    });
</script>
@endsection
