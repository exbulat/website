<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Спасибо за участие</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('public/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/survey.css') }}">
    
    <style>
        :root {
            --primary-color: {{ $survey->design['primary_color'] ?? '#4e73df' }};
            --background-color: {{ $survey->design['background_color'] ?? '#ffffff' }};
            --font-family: {{ $survey->design['font'] ?? 'Open Sans' }}, sans-serif;
            --image-opacity: {{ $survey->design['image_opacity'] ?? '0.3' }};
        }
        
        body {
            font-family: var(--font-family);
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            @if(!empty($survey->design['background_image']))
            position: relative;
            @endif
        }
        
        /* Фоновое изображение с настройкой прозрачности */
        @if(!empty($survey->design['background_image']))
        body::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-image: url('{{ asset('storage/' . $survey->design['background_image']) }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            opacity: var(--image-opacity);
            z-index: -1;
        }
        @endif
        
        .survey-container {
            background-color: var(--background-color);
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin: 2rem auto;
            max-width: 800px;
            width: 100%;
        }
        
        .survey-header {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: color-mix(in srgb, var(--primary-color) 80%, black);
            border-color: color-mix(in srgb, var(--primary-color) 80%, black);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="survey-container">
            <div class="survey-header">
                <div>
                    <h1>{{ $survey->title }}</h1>
                    @if($survey->description)
                        <p class="lead">{{ $survey->description }}</p>
                    @endif
                </div>
            </div>
            
            <div class="p-4">
                <div class="text-center p-4">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    
                    <h2 class="mb-3">Спасибо за участие в опросе!</h2>
                    
                    <p class="lead mb-4">
                        Ваши ответы были успешно сохранены.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <a href="{{ route('surveys.view-responses', $survey->code) }}" class="btn btn-outline-primary">
                            <i class="fas fa-list-check me-2"></i>Просмотреть свои ответы
                        </a>
                        
                        @auth
                            @if(Auth::id() === $survey->user_id)
                                <a href="{{ route('surveys.results', $survey) }}" class="btn btn-primary">
                                    <i class="fas fa-chart-bar me-2"></i>Посмотреть общие результаты
                                </a>
                            @endif
                        @endauth
                    </div>
                    
                    @if($survey->social_sharing)
                        <div class="mt-5">
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
                    @endif
                    
                    @if($survey->custom_thank_you_message)
                        <div class="mt-4 text-start">
                            <h5>Сообщение от автора опроса</h5>
                            <div class="card">
                                <div class="card-body">
                                    {!! nl2br(e($survey->custom_thank_you_message)) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($survey->redirect_url)
                        <div class="mt-4">
                            <p class="text-muted">Вы будете перенаправлены через <span id="countdown">5</span> секунд...</p>
                        </div>
                    @endif
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-link">
                            <i class="fas fa-home me-2"></i>Вернуться на главную
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script src="{{ asset('public/js/scripts.js') }}"></script>
    
    @if($survey->redirect_url)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let secondsLeft = 5;
            const countdownElement = document.getElementById('countdown');
            
            const interval = setInterval(function() {
                secondsLeft--;
                countdownElement.textContent = secondsLeft;
                
                if (secondsLeft <= 0) {
                    clearInterval(interval);
                    window.location.href = "{{ $survey->redirect_url }}";
                }
            }, 1000);
        });
    </script>
    @endif
</body>
</html>
