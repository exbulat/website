@extends('layouts.app')

@section('title', 'Уведомления')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Уведомления</h1>
                
                @if($notifications->count() > 0)
                    <div>
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-check-double me-1"></i>Отметить все как прочитанные
                            </button>
                        </form>
                    </div>
                @endif
            </div>
            
            @if($notifications->count() > 0)
                <div class="card shadow-sm">
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ !$notification->is_read ? 'bg-light' : '' }}">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($notification->type === 'new_response')
                                            <div class="notification-icon bg-primary text-white">
                                                <i class="fas fa-comment"></i>
                                            </div>
                                        @elseif($notification->type === 'survey_completed')
                                            <div class="notification-icon bg-success text-white">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        @else
                                            <div class="notification-icon bg-info text-white">
                                                <i class="fas fa-bell"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('surveys.results', $notification->survey) }}" class="text-decoration-none text-dark">
                                                <h6 class="mb-0 {{ !$notification->is_read ? 'fw-bold' : '' }}">{{ $notification->message }}</h6>
                                            </a>
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary ms-2">Новое</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    @if(!$notification->is_read)
                                        <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="me-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Вы уверены, что хотите удалить это уведомление?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bell fa-4x text-muted mb-3"></i>
                        <h5>У вас пока нет уведомлений</h5>
                        <p class="text-muted">Уведомления появятся, когда кто-то ответит на ваш опрос или произойдут другие важные события.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection
