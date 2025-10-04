@extends('admin.layouts.app')

@section('title', 'Создание пользователя')

@section('content')
<!-- Заголовок страницы -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Создание нового пользователя</h1>
    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Назад к списку
    </a>
</div>

<!-- Форма создания пользователя -->
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Имя пользователя</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_admin">
                        Администратор
                    </label>
                    <div class="form-text">Администраторы имеют доступ к панели управления и всем функциям сайта</div>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" value="1" {{ old('is_super_admin') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_super_admin">
                        Суперадминистратор
                    </label>
                    <div class="form-text text-danger">Суперадминистраторы имеют доступ к логам активности и всем административным функциям</div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Создать пользователя
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
