@extends('layouts.app')

@section('title', 'Редактирование вопроса')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Редактирование вопроса</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('surveys.questions.update', [$survey, $question]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Текст вопроса <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $question->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание или подсказка</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description', $question->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Необязательное пояснение к вопросу</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Тип вопроса <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Выберите тип вопроса</option>
                                @foreach($questionTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $question->type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="options-container" class="mb-3 d-none">
                            <label class="form-label">Варианты ответов <span class="text-danger">*</span></label>
                            <div class="options-list">
                                @if(old('options', $question->options))
                                    @foreach(old('options', $question->options) as $index => $option)
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="options[]" value="{{ $option }}" placeholder="Вариант ответа {{ $index + 1 }}">
                                            <button type="button" class="btn btn-outline-danger remove-option" {{ count(old('options', $question->options)) <= 2 ? 'disabled' : '' }}>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="options[]" placeholder="Вариант ответа 1">
                                        <button type="button" class="btn btn-outline-danger remove-option" disabled>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="options[]" placeholder="Вариант ответа 2">
                                        <button type="button" class="btn btn-outline-danger remove-option" disabled>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary add-option">
                                <i class="fas fa-plus me-2"></i>Добавить вариант
                            </button>
                        </div>
                        
                        <div id="scale-container" class="mb-3 d-none">
                            <label class="form-label">Настройки шкалы</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="scale_min" class="form-label">Минимальное значение</label>
                                        <input type="number" class="form-control" id="scale_min" name="scale_min" value="{{ old('scale_min', $question->options['min'] ?? 1) }}" min="1" max="99">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="scale_max" class="form-label">Максимальное значение</label>
                                        <input type="number" class="form-control" id="scale_max" name="scale_max" value="{{ old('scale_max', $question->options['max'] ?? 10) }}" min="2" max="100">
                                    </div>
                                </div>
                            </div>
                            <div class="form-text">Укажите минимальное и максимальное значение для шкалы (макс. 100)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="position" class="form-label">Позиция вопроса</label>
                            <input type="number" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $question->position) }}" min="1">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1" {{ old('is_required', $question->is_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">Обязательный вопрос</label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="time_limit" class="form-label">Ограничение времени на вопрос (в секундах)</label>
                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" value="{{ old('time_limit', $question->time_limit) }}" min="0">
                            @error('time_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Оставьте пустым, чтобы не ограничивать время на ответ</div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('surveys.show', $survey) }}" class="btn btn-outline-secondary">Отмена</a>
                            <button type="submit" class="btn btn-primary">Сохранить</button>
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
        const typeSelect = document.getElementById('type');
        const optionsContainer = document.getElementById('options-container');
        const scaleContainer = document.getElementById('scale-container');
        const addOptionBtn = document.querySelector('.add-option');
        
        // Обработка изменения типа вопроса
        typeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            
            // Скрываем все контейнеры специфичных настроек
            optionsContainer.classList.add('d-none');
            scaleContainer.classList.add('d-none');
            
            // Показываем нужный контейнер в зависимости от типа
            if (selectedType === 'single_choice' || selectedType === 'multiple_choice') {
                optionsContainer.classList.remove('d-none');
            } else if (selectedType === 'scale') {
                scaleContainer.classList.remove('d-none');
            }
        });
        
        // Если уже выбран тип при загрузке страницы
        if (typeSelect.value) {
            typeSelect.dispatchEvent(new Event('change'));
        }
        
        // Добавление нового варианта ответа
        addOptionBtn.addEventListener('click', function() {
            const optionsList = document.querySelector('.options-list');
            const optionsCount = optionsList.querySelectorAll('.input-group').length;
            
            const newOption = document.createElement('div');
            newOption.className = 'input-group mb-2';
            newOption.innerHTML = `
                <input type="text" class="form-control" name="options[]" placeholder="Вариант ответа ${optionsCount + 1}">
                <button type="button" class="btn btn-outline-danger remove-option">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            optionsList.appendChild(newOption);
            
            // Активируем все кнопки удаления, если вариантов больше 2
            if (optionsList.querySelectorAll('.input-group').length > 2) {
                optionsList.querySelectorAll('.remove-option').forEach(btn => {
                    btn.disabled = false;
                });
            }
        });
        
        // Удаление варианта ответа (делегирование событий)
        document.querySelector('.options-list').addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                const optionsList = document.querySelector('.options-list');
                const optionsCount = optionsList.querySelectorAll('.input-group').length;
                
                // Удаляем только если осталось больше 2 вариантов
                if (optionsCount > 2) {
                    e.target.closest('.input-group').remove();
                    
                    // Если осталось ровно 2 варианта, деактивируем кнопки удаления
                    if (optionsList.querySelectorAll('.input-group').length === 2) {
                        optionsList.querySelectorAll('.remove-option').forEach(btn => {
                            btn.disabled = true;
                        });
                    }
                }
            }
        });
    });
</script>
@endsection
