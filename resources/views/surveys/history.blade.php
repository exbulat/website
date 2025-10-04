@extends('layouts.app')

@section('title', 'История опросов')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">История опросов</h1>
                
                <a href="{{ route('surveys.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Создать опрос
                </a>
            </div>

            <!-- Новая реализация модального окна -->
            <div id="customModal" class="custom-modal" style="display: none;">
                <div class="custom-modal-overlay"></div>
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h5 class="modal-title">Подтверждение удаления</h5>
                        <button type="button" class="custom-close-btn">&times;</button>
                    </div>
                    <div class="custom-modal-body">
                        <p class="text-danger fw-bold">ВНИМАНИЕ! Опрос будет удален полностью без возможности восстановления.</p>
                        <p id="modalMessage"></p>
                    </div>
                    <div class="custom-modal-footer">
                        <button type="button" class="btn btn-secondary custom-cancel-btn">Отмена</button>
                        <form id="deleteForm" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Удалить полностью</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Информация об архиве -->
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-archive fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Архив опросов</h5>
                        <p class="mb-0">У вас {{ $archivedCount }} архивированных опросов. Вы можете просмотреть их и их результаты в архиве.</p>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('surveys.archive.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-archive me-1"></i>Перейти в архив
                        </a>
                    </div>
                </div>
            </div>
            
            @if($surveys->total() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Статистика</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="text-center">
                                    <h2 class="h1 text-primary">{{ $surveys->count() }}</h2>
                                    <p class="text-muted mb-0">Всего опросов</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="text-center">
                                    <h2 class="h1 text-success">{{ $activeCount }}</h2>
                                    <p class="text-muted mb-0">Активных</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="text-center">
                                    <h2 class="h1 text-info">{{ $totalResponses }}</h2>
                                    <p class="text-muted mb-0">Всего ответов</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Ваши опросы</h5>
                            <span class="badge bg-primary">{{ $surveys->count() }} опросов</span>
                        </div>
                        <div class="filter-buttons">
                            <div class="btn-group w-100">
                                <a href="{{ route('surveys.history') }}" class="btn {{ !request('filter') ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                    Все
                                </a>
                                <a href="{{ route('surveys.history', ['filter' => 'active']) }}" class="btn {{ request('filter') == 'active' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                    Активные
                                </a>
                                <a href="{{ route('surveys.history', ['filter' => 'inactive']) }}" class="btn {{ request('filter') == 'inactive' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                    Неактивные
                                </a>
                                <a href="{{ route('surveys.history', ['filter' => 'public']) }}" class="btn {{ request('filter') == 'public' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                    Публичные
                                </a>
                                <a href="{{ route('surveys.history', ['filter' => 'private']) }}" class="btn {{ request('filter') == 'private' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                    Приватные
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Название</th>
                                    <th>Дата создания</th>
                                    <th>Статус</th>
                                    <th>Ответов</th>
                                    <th>Последний ответ</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($surveys as $survey)
                                    <tr>
                                        <td>
                                            <a href="{{ route('surveys.show', $survey) }}" class="text-decoration-none text-dark fw-medium">
                                                {{ $survey->title }}
                                            </a>
                                        </td>
                                        <td>{{ $survey->created_at->format('d.m.Y') }}</td>
                                        <td>
                                            @if($survey->isActive())
                                                <span class="badge bg-success">Активен</span>
                                            @else
                                                <span class="badge bg-secondary">Неактивен</span>
                                            @endif
                                            
                                            @if($survey->is_public)
                                                <span class="badge bg-info">Публичный</span>
                                            @else
                                                <span class="badge bg-warning">Приватный</span>
                                            @endif
                                        </td>
                                        <td>{{ $survey->answers()->select('session_id')->distinct()->count() }}</td>
                                        <td>
                                            @php
                                                $lastAnswer = $survey->answers()->latest()->first();
                                            @endphp
                                            {{ $lastAnswer ? $lastAnswer->created_at->diffForHumans() : 'Нет ответов' }}
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('surveys.results', $survey) }}" class="btn btn-sm btn-outline-primary" title="Результаты">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <a href="{{ route('surveys.edit', $survey) }}" class="btn btn-sm btn-outline-secondary" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('surveys.qrcode', $survey) }}" class="btn btn-sm btn-outline-info" title="QR-код">
                                                    <i class="fas fa-qrcode"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-survey-btn" title="Удалить полностью" 
                                                        data-survey-id="{{ $survey->id }}" 
                                                        data-survey-title="{{ $survey->title }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    {{ $surveys->links() }}
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-filter fa-4x text-muted mb-3"></i>
                        @php
                            $filterLabels = [
                                'active' => 'активных',
                                'inactive' => 'неактивных',
                                'public' => 'публичных',
                                'private' => 'приватных'
                            ];
                            $currentFilter = request('filter');
                        @endphp
                        
                        @if(request('filter') && array_key_exists($currentFilter, $filterLabels))
                            <h5>Нет {{ $filterLabels[$currentFilter] }} опросов</h5>
                            <p class="text-muted">По выбранному фильтру не найдено опросов.</p>
                            <a href="{{ route('surveys.history') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-list me-1"></i>Показать все опросы
                            </a>
                        @else
                            <h5>У вас пока нет опросов</h5>
                            <p class="text-muted">Создайте свой первый опрос, чтобы начать собирать ответы.</p>
                            <a href="{{ route('surveys.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i>Создать опрос
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.custom-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1999;
}

.custom-modal-content {
    background: white;
    padding: 20px;
    border-radius: 5px;
    width: 90%;
    max-width: 500px;
    position: relative;
    z-index: 2001;
}

.custom-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.custom-close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    color: #666;
}

.custom-modal-body {
    margin-bottom: 20px;
}

.custom-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.accessibility-btn {
    z-index: 1998 !important;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('customModal');
    const deleteButtons = document.querySelectorAll('.delete-survey-btn');
    const closeBtn = document.querySelector('.custom-close-btn');
    const cancelBtn = document.querySelector('.custom-cancel-btn');
    const overlay = document.querySelector('.custom-modal-overlay');
    const deleteForm = document.getElementById('deleteForm');
    
    function showModal(surveyId, surveyTitle) {
        document.getElementById('modalMessage').textContent = `Вы уверены, что хотите удалить опрос "${surveyTitle}"?`;
        deleteForm.action = `/surveys/${surveyId}`;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function hideModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const surveyId = button.dataset.surveyId;
            const surveyTitle = button.dataset.surveyTitle;
            showModal(surveyId, surveyTitle);
        });
    });
    
    closeBtn.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);
    overlay.addEventListener('click', hideModal);
    
    deleteForm.addEventListener('submit', () => {
        hideModal();
    });
});
</script>
@endsection
