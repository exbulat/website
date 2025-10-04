@extends('layouts.app')

@section('title', 'Создание опроса')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Создание нового опроса</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('surveys.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Название опроса <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Введите понятное название для вашего опроса</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Опишите цель опроса или дайте инструкции для участников</div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_at" class="form-label">Дата начала</label>
                                <input type="datetime-local" class="form-control @error('start_at') is-invalid @enderror" id="start_at" name="start_at" value="{{ old('start_at') }}">
                                @error('start_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Оставьте пустым, чтобы начать сразу</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_at" class="form-label">Дата окончания</label>
                                <input type="datetime-local" class="form-control @error('end_at') is-invalid @enderror" id="end_at" name="end_at" value="{{ old('end_at') }}">
                                @error('end_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Оставьте пустым, чтобы не ограничивать по времени</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="time_limit" class="form-label">Ограничение времени (в секундах)</label>
                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" value="{{ old('time_limit') }}" min="0">
                            @error('time_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Оставьте пустым, чтобы не ограничивать время на прохождение</div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Активный опрос</label>
                            </div>
                            <div class="form-text">Неактивные опросы не могут быть пройдены</div>
                        </div>
                        
                        <div class="mb-4">
                            <input type="hidden" name="show_results" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_results" name="show_results" value="1" {{ old('show_results', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_results">Показывать результаты</label>
                            </div>
                            <div class="form-text">Если включено, результаты опроса будут доступны для просмотра после его завершения</div>
                        </div>
                        
                        <div class="mb-3">
                            <input type="hidden" name="is_public" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">Публичный опрос</label>
                            </div>
                            <div class="form-text">Публичные опросы доступны всем по ссылке, закрытые опросы могут проходить только те, у кого есть QR-код или ссылка</div>
                        </div>
                        
                        <div class="mb-4" id="access-code-container">
                            <label for="access_code" class="form-label">Код доступа</label>
                            <input type="text" class="form-control @error('access_code') is-invalid @enderror" id="access_code" name="access_code" value="{{ old('access_code') }}">
                            @error('access_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Если указан, пользователи должны будут ввести этот код для доступа к опросу</div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Настройки дизайна</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="design[primary_color]" class="form-label">Основной цвет</label>
                                        <input type="color" class="form-control form-control-color w-100" id="design[primary_color]" name="design[primary_color]" value="{{ old('design.primary_color', '#4e73df') }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="design[background_color]" class="form-label">Цвет фона</label>
                                        <input type="color" class="form-control form-control-color w-100" id="design[background_color]" name="design[background_color]" value="{{ old('design.background_color', '#ffffff') }}">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="design[font]" class="form-label">Шрифт</label>
                                    <select class="form-select @error('design.font') is-invalid @enderror" id="design[font]" name="design[font]">
                                        <option value="Arial" {{ old('design.font') == 'Arial' ? 'selected' : '' }}>Arial</option>
                                        <option value="Roboto" {{ old('design.font') == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                        <option value="Open Sans" {{ old('design.font', 'Open Sans') == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                        <option value="Montserrat" {{ old('design.font') == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="background_image" class="form-label">Фоновое изображение</label>
                                    <input type="file" class="form-control @error('background_image') is-invalid @enderror" id="background_image" name="background_image" accept="image/*">
                                    @error('background_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Загрузите изображение для фона опроса (JPG, PNG, размер до 2MB)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="design[image_opacity]" class="form-label">Прозрачность фонового изображения: <span id="opacity-value">0.3</span></label>
                                    <input type="range" class="form-range" id="design[image_opacity]" name="design[image_opacity]" min="0" max="1" step="0.1" value="{{ old('design.image_opacity', '0.3') }}">
                                    <div class="form-text">Настройте прозрачность фонового изображения (0 - полностью прозрачно, 1 - непрозрачно)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('surveys.index') }}" class="btn btn-outline-secondary">Отмена</a>
                            <button type="submit" class="btn btn-primary">Создать опрос</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isPublicCheckbox = document.getElementById('is_public');
        const accessCodeContainer = document.getElementById('access-code-container');
        
        function toggleAccessCodeVisibility() {
            if (isPublicCheckbox.checked) {
                accessCodeContainer.style.display = 'none';
            } else {
                accessCodeContainer.style.display = 'block';
            }
        }
        
        // Инициализация при загрузке страницы
        toggleAccessCodeVisibility();
        
        // Обработчик изменения чекбокса
        isPublicCheckbox.addEventListener('change', toggleAccessCodeVisibility);
        
        // Обработчик изменения ползунка прозрачности
        const opacitySlider = document.getElementById('design[image_opacity]');
        const opacityValue = document.getElementById('opacity-value');
        
        if (opacitySlider && opacityValue) {
            opacitySlider.addEventListener('input', function() {
                opacityValue.textContent = this.value;
            });
        }
    });
</script>
@endsection
