@extends('layouts.app')

@section('title', 'Детали лога активности')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary me-3">
                        <i class="fas fa-arrow-left me-1"></i> Назад в админ-панель
                    </a>
                    <h1 class="mb-0">Детали лога активности #{{ $log->id }}</h1>
                </div>
                <div>
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list me-1"></i> Назад к списку
                    </a>
                    <form action="{{ route('admin.logs.destroy', $log) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот лог?')">
                            <i class="fas fa-trash me-1"></i> Удалить
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i> Основная информация
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">ID:</dt>
                                <dd class="col-sm-8">{{ $log->id }}</dd>
                                
                                <dt class="col-sm-4">Пользователь:</dt>
                                <dd class="col-sm-8">
                                    @if($log->user)
                                        <a href="{{ route('admin.users.edit', $log->user) }}">{{ $log->user->name }}</a>
                                    @else
                                        <span class="text-muted">Система</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Действие:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-{{ $log->action == 'create' ? 'success' : ($log->action == 'update' ? 'primary' : ($log->action == 'delete' ? 'danger' : 'secondary')) }}">
                                        {{ $log->action_name }}
                                    </span>
                                </dd>
                                
                                <dt class="col-sm-4">Тип сущности:</dt>
                                <dd class="col-sm-8">{{ $log->entity_name ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">ID сущности:</dt>
                                <dd class="col-sm-8">{{ $log->entity_id ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">IP-адрес:</dt>
                                <dd class="col-sm-8">{{ $log->ip_address }}</dd>
                                
                                <dt class="col-sm-4">User-Agent:</dt>
                                <dd class="col-sm-8">
                                    <small class="text-muted">{{ Str::limit($log->user_agent, 50) }}</small>
                                    <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#userAgentModal">
                                        Показать полностью
                                    </button>
                                </dd>
                                
                                <dt class="col-sm-4">Дата и время:</dt>
                                <dd class="col-sm-8">{{ $log->created_at->format('d.m.Y H:i:s') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-align-left me-1"></i> Описание
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $log->description ?? 'Нет описания' }}</p>
                </div>
            </div>

            @if($log->old_values || $log->new_values)
                <div class="row">
                    @if($log->old_values)
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-history me-1"></i> Старые значения
                                </div>
                                <div class="card-body">
                                    <pre class="mb-0"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($log->new_values)
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-pencil-alt me-1"></i> Новые значения
                                </div>
                                <div class="card-body">
                                    <pre class="mb-0"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Модальное окно для просмотра полного User-Agent -->
<div class="modal fade" id="userAgentModal" tabindex="-1" aria-labelledby="userAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userAgentModalLabel">User-Agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre class="mb-0"><code>{{ $log->user_agent }}</code></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
@endsection
