@props(['url', 'title', 'description' => '', 'image' => '', 'compact' => false])

<style>
    .share-buttons-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .share-buttons-sm .btn i {
        font-size: 0.8rem;
    }
    
    .share-buttons-sm .text-muted {
        font-size: 0.75rem;
    }
    
    .share-buttons-icon-only .btn span {
        display: none;
    }
    
    .share-buttons-icon-only .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Адаптивные стили для мобильных устройств */
    @media (max-width: 767px) {
        .share-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .share-buttons .btn {
            margin: 2px;
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .share-buttons .btn i {
            font-size: 1rem;
        }
        
        .share-buttons-responsive {
            display: flex;
            justify-content: flex-start;
        }
        
        .share-buttons-responsive .btn {
            border-radius: 4px;
            margin-right: 4px;
        }
    }
</style>

<div {{ $attributes->merge(['class' => 'share-buttons']) }}>
    <div class="d-flex gap-1 align-items-center flex-wrap">
        @if(!$attributes->has('class') || !str_contains($attributes->get('class'), 'share-buttons-sm'))
            <span class="text-muted me-1">Поделиться:</span>
        @endif
        
        {{-- VK --}}
        <a href="https://vk.com/share.php?url={{ urlencode($url) }}&title={{ urlencode($title) }}&description={{ urlencode($description) }}&image={{ urlencode($image) }}" 
           target="_blank" 
           class="btn btn-sm btn-outline-primary" 
           title="Поделиться ВКонтакте">
            <i class="fab fa-vk"></i>
            @if($compact)
                <span>ВК</span>
            @endif
        </a>
        
        {{-- Telegram --}}
        <a href="https://t.me/share/url?url={{ urlencode($url) }}&text={{ urlencode($title) }}" 
           target="_blank" 
           class="btn btn-sm btn-outline-primary" 
           title="Поделиться в Telegram">
            <i class="fab fa-telegram-plane"></i>
            @if($compact)
                <span>Telegram</span>
            @endif
        </a>
        
        {{-- WhatsApp --}}
        <a href="https://api.whatsapp.com/send?text={{ urlencode($title . ' ' . $url) }}" 
           target="_blank" 
           class="btn btn-sm btn-outline-primary" 
           title="Поделиться в WhatsApp">
            <i class="fab fa-whatsapp"></i>
            @if($compact)
                <span>WhatsApp</span>
            @endif
        </a>
        
        {{-- Email --}}
        <a href="mailto:?subject={{ urlencode($title) }}&body={{ urlencode($description . "\n\n" . $url) }}" 
           class="btn btn-sm btn-outline-primary" 
           title="Отправить по email">
            <i class="fas fa-envelope"></i>
            @if($compact)
                <span>Email</span>
            @endif
        </a>
        
        {{-- Copy Link --}}
        <button type="button" 
                class="btn btn-sm btn-outline-primary copy-link" 
                data-clipboard-text="{{ $url }}"
                title="Копировать ссылку">
            <i class="fas fa-link"></i>
            @if($compact)
                <span>Копировать</span>
            @endif
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация кнопки копирования ссылки
        const copyButtons = document.querySelectorAll('.copy-link');
        
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const text = this.getAttribute('data-clipboard-text');
                navigator.clipboard.writeText(text).then(() => {
                    // Временно меняем иконку для обратной связи
                    const icon = this.querySelector('i');
                    const originalClass = icon.className;
                    icon.className = 'fas fa-check';
                    
                    setTimeout(() => {
                        icon.className = originalClass;
                    }, 2000);
                    
                    // Показываем уведомление
                    const tooltip = document.createElement('div');
                    tooltip.className = 'position-fixed bottom-0 end-0 p-3';
                    tooltip.innerHTML = `
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <i class="fas fa-link me-2"></i>
                                <strong class="me-auto">Ссылка скопирована</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                Ссылка скопирована в буфер обмена
                            </div>
                        </div>
                    `;
                    
                    document.body.appendChild(tooltip);
                    
                    setTimeout(() => {
                        tooltip.remove();
                    }, 3000);
                }).catch(err => {
                    console.error('Не удалось скопировать текст: ', err);
                });
            });
        });
    });
</script>
@endpush
