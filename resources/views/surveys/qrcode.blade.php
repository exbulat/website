@extends('layouts.app')

@section('title', 'QR-код для опроса: ' . $survey->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="mb-0">QR-код для опроса: {{ $survey->title }}</h3>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <div class="qr-code-container border p-3 d-inline-block">
                            <img src="{{ $qrCodeUrl }}" alt="QR-код для опроса" class="img-fluid" id="qr-code-image" width="250" height="250" style="width: 250px; height: 250px;" onerror="handleQrCodeError()">
                        </div>
                    </div>
                    
                    <p class="lead mb-4">
                        Отсканируйте QR-код для прохождения опроса или поделитесь ссылкой:
                    </p>
                    
                    <div class="input-group mb-4">
                        <input type="text" class="form-control" id="survey-url" value="{{ route('surveys.take', $survey->code) }}" readonly>
                        <button class="btn btn-outline-primary copy-btn" type="button" data-clipboard-target="#survey-url">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <a href="#" class="btn btn-primary" id="download-qr">
                            <i class="fas fa-download me-2"></i>Скачать QR-код
                        </a>
                        <a href="#" class="btn btn-outline-primary" id="print-qr">
                            <i class="fas fa-print me-2"></i>Распечатать
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <p class="text-muted mb-3">Поделиться опросом:</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="https://vk.com/share.php?url={{ urlencode(route('surveys.take', $survey->code)) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fab fa-vk"></i>
                            </a>
                            <a href="https://t.me/share/url?url={{ urlencode(route('surveys.take', $survey->code)) }}&text={{ urlencode($survey->title) }}" target="_blank" class="btn btn-outline-info">
                                <i class="fab fa-telegram"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($survey->title . ' - ' . route('surveys.take', $survey->code)) }}" target="_blank" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="mailto:?subject={{ urlencode($survey->title) }}&body={{ urlencode('Примите участие в опросе: ' . route('surveys.take', $survey->code)) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('surveys.show', $survey->id) }}" class="btn btn-link">
                    <i class="fas fa-arrow-left me-2"></i>Вернуться к опросу
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Скрытый div для печати -->
<div id="print-container" class="d-none">
    <div style="text-align: center; padding: 20px;">
        <h2 style="margin-bottom: 15px;">{{ $survey->title }}</h2>
        <div style="margin: 20px auto; width: 300px; height: 300px;">
            <img src="{{ $qrCodeUrl }}" alt="QR-код для опроса" style="width: 100%; height: 100%;">
        </div>
        <p style="margin-top: 15px; font-size: 16px;">Отсканируйте QR-код для прохождения опроса</p>
        <p style="margin-top: 5px; font-size: 14px;">{{ route('surveys.take', $survey->code) }}</p>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
        // Функция для обработки ошибок загрузки QR-кода
    function handleQrCodeError() {
        const img = document.getElementById('qr-code-image');
        const url = document.getElementById('survey-url').value;
        // Используем альтернативный сервис, если основной не сработал
        img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(url);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Делаем функцию handleQrCodeError глобальной
        window.handleQrCodeError = handleQrCodeError;
        
        // Инициализация Clipboard.js
        const clipboard = new ClipboardJS('.copy-btn');
        
        clipboard.on('success', function(e) {
            const button = e.trigger;
            const originalContent = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-success');
            
            setTimeout(function() {
                button.innerHTML = originalContent;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
            }, 2000);
            
            e.clearSelection();
        });
        
        // Скачивание QR-кода - простой подход через прямое скачивание изображения
        document.getElementById('download-qr').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Получаем URL QR-кода
            const qrUrl = document.getElementById('qr-code-image').src;
            
            // Создаем временный элемент для скачивания
            fetch(qrUrl)
                .then(response => response.blob())
                .then(blob => {
                    // Создаем объект URL из blob
                    const blobUrl = URL.createObjectURL(blob);
                    
                    // Создаем ссылку для скачивания
                    const link = document.createElement('a');
                    link.href = blobUrl;
                    link.download = 'qr-код_{{ $survey->code }}.png';
                    
                    // Добавляем ссылку в DOM, кликаем по ней и удаляем
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Освобождаем объект URL
                    setTimeout(() => URL.revokeObjectURL(blobUrl), 100);
                })
                .catch(error => {
                    console.error('Ошибка при скачивании QR-кода:', error);
                    alert('Произошла ошибка при скачивании QR-кода. Пожалуйста, попробуйте еще раз.');
                });
        });
        
        // Печать QR-кода
        document.getElementById('print-qr').addEventListener('click', function(e) {
            e.preventDefault();
            
            const printContainer = document.getElementById('print-container');
            const originalDisplay = printContainer.style.display;
            
            printContainer.classList.remove('d-none');
            
            window.print();
            
            setTimeout(function() {
                printContainer.classList.add('d-none');
            }, 500);
        });
    });
</script>
@endsection

@section('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        
        #print-container, #print-container * {
            visibility: visible;
        }
        
        #print-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
        }
    }
    
    .qr-code-container {
        background-color: white;
        display: inline-block;
        padding: 15px;
        border-radius: 5px;
    }
    
    .qr-code-container svg {
        width: 250px;
        height: 250px;
    }
</style>
@endsection
