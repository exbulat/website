@extends('admin.layouts.app')

@section('title', 'Управление пользователями')

@section('styles')
<style>
    .user-table {
        background-color: #fff;
    }
    
    .user-table th {
        font-weight: 600;
    }
    
    .filter-card {
        margin-bottom: 1.5rem;
        background-color: #f8f9fc;
        border: none;
    }
    
    .badge-admin {
        background-color: #4e73df;
        color: white;
    }
    
    .badge-user {
        background-color: #1cc88a;
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
</style>
@endsection

@section('content')
<!-- Заголовок страницы -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Управление пользователями</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus me-2"></i>Создать пользователя
    </a>
</div>

<!-- Фильтры -->
<div class="card shadow-sm filter-card">
    <div class="card-body">
        <form action="{{ route('admin.users') }}" method="GET" class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Поиск по имени или email" name="search" value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <div class="col-md-3 mb-3 mb-md-0">
                <select class="form-select" name="role" onchange="this.form.submit()">
                    <option value="">Все роли</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Администраторы</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Пользователи</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3 mb-md-0">
                <select class="form-select" name="sort" onchange="this.form.submit()">
                    <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Дата регистрации</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Имя</option>
                    <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
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

<!-- Таблица пользователей -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped user-table mb-0">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="sorting-header {{ request('sort') == 'id' ? (request('order', 'desc') == 'desc' ? 'desc' : 'asc') : '' }}" data-sort="id">#</th>
                        <th scope="col" class="sorting-header {{ request('sort') == 'name' ? (request('order', 'desc') == 'desc' ? 'desc' : 'asc') : '' }}" data-sort="name">Имя</th>
                        <th scope="col" class="sorting-header {{ request('sort') == 'email' ? (request('order', 'desc') == 'desc' ? 'desc' : 'asc') : '' }}" data-sort="email">Email</th>
                        <th scope="col">Роль</th>
                        <th scope="col" class="sorting-header {{ request('sort') == 'created_at' ? (request('order', 'desc') == 'desc' ? 'desc' : 'asc') : '' }}" data-sort="created_at">Зарегистрирован</th>
                        <th scope="col" class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <th scope="row">{{ $user->id }}</th>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->is_admin)
                                    <span class="badge rounded-pill badge-admin">Администратор</span>
                                @else
                                    <span class="badge rounded-pill badge-user">Пользователь</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    @if(!$user->is_super_admin || auth()->user()->is_super_admin)
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary action-btn" data-bs-toggle="tooltip" title="Редактировать">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if(auth()->id() !== $user->id && (!$user->is_super_admin || auth()->user()->is_super_admin))
                                            <button type="button" class="btn btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}" title="Удалить">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Пользователей не найдено</td>
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
                Показано {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} из {{ $users->total() }} пользователей
            </div>
            <div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Модальные окна для подтверждения удаления -->
@foreach($users as $user)
    @if(!$user->is_super_admin || auth()->user()->is_super_admin)
    <div class="modal" id="deleteModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">Подтверждение удаления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Вы действительно хотите удалить пользователя <strong>{{ $user->name }}</strong>?</p>
                    <p class="text-danger">Это действие невозможно отменить!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cancel-modal">Отмена</button>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
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
