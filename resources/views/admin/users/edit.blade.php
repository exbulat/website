@extends('admin.layouts.app')

@section('title', 'Редактирование пользователя')

@section('content')
<!-- Заголовок страницы -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Редактирование пользователя</h1>
    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Назад к списку
    </a>
</div>

<!-- Форма редактирования пользователя -->
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="name" class="form-label">Имя пользователя</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Новый пароль <span class="text-muted">(оставьте пустым, если не хотите менять)</span></label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Подтверждение нового пароля</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_admin">
                        Администратор
                    </label>
                    <div class="form-text">Администраторы имеют доступ к панели управления и всем функциям сайта</div>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" value="1" {{ old('is_super_admin', $user->is_super_admin) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_super_admin">
                        Суперадминистратор
                    </label>
                    <div class="form-text text-danger">Суперадминистраторы имеют доступ к логам активности и всем административным функциям</div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Статистика пользователя -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Статистика пользователя</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <div class="mb-2 font-weight-bold">ID пользователя:</div>
                        <div>{{ $user->id }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-2 font-weight-bold">Зарегистрирован:</div>
                        <div>{{ $user->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <div class="mb-2 font-weight-bold">Последний вход:</div>
                        <div>{{ $user->last_login_at ?? 'Нет данных' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-2 font-weight-bold">Создано опросов:</div>
                        <div>{{ $user->surveys->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Действия</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.surveys', ['user_id' => $user->id]) }}" class="btn btn-info">
                        <i class="fas fa-poll me-2"></i>Просмотреть опросы пользователя
                    </a>
                    
                    @if(auth()->id() !== $user->id)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                            <i class="fas fa-trash me-2"></i>Удалить пользователя
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы действительно хотите удалить пользователя <strong>{{ $user->name }}</strong>?</p>
                <p class="text-danger">Это действие нельзя отменить! Все опросы и данные пользователя также будут удалены.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить пользователя</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
