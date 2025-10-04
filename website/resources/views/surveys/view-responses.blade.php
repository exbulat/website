<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ваши ответы: {{ $survey->title }}</title>
    
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
        
        .question-number {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .user-answer {
            background-color: #e9f7f9;
            border-left: 3px solid var(--primary-color);
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .selected-option {
            font-weight: bold;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="survey-container">
            <div class="survey-header">
                <div>
                    <h1>Ваши ответы</h1>
                    <p class="lead">{{ $survey->title }}</p>
                    @if($survey->description)
                        <p>{{ $survey->description }}</p>
                    @endif
                </div>
            </div>
            
            <div class="p-4">
                @if(isset($error))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $error }}
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Вернуться на главную
                        </a>
                    </div>
                @else
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Вы уже прошли этот опрос. Ниже представлены ваши ответы.
                    </div>
                    
                    @if($questions->isEmpty())
                        <div class="alert alert-warning">
                            В этом опросе нет вопросов.
                        </div>
                    @else
                        @foreach($questions as $index => $question)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <span class="question-number">Вопрос {{ $index + 1 }}</span>
                                    <h5 class="mt-1 mb-0">{{ $question->title }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($question->description)
                                        <p class="text-muted">{{ $question->description }}</p>
                                    @endif
                                    
                                    <h6 class="mt-3">Ваш ответ:</h6>
                                    <div class="user-answer">
                                        @if($question->type === 'single_choice')
                                            @isset($formattedAnswers[$question->id][0])
                                                {{ $formattedAnswers[$question->id][0] }}
                                            @else
                                                <em class="text-muted">Нет ответа</em>
                                            @endisset
                                            
                                        @elseif($question->type === 'multiple_choice')
                                            @if(isset($formattedAnswers[$question->id]) && !empty($formattedAnswers[$question->id]))
                                                <ul class="mb-0">
                                                    @foreach($formattedAnswers[$question->id] as $answer)
                                                        <li>{{ $answer }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <em class="text-muted">Нет ответа</em>
                                            @endif
                                            
                                        @elseif($question->type === 'text')
                                            @isset($formattedAnswers[$question->id][0])
                                                {{ $formattedAnswers[$question->id][0] }}
                                            @else
                                                <em class="text-muted">Нет ответа</em>
                                            @endisset
                                            
                                        @elseif($question->type === 'scale')
                                            @isset($formattedAnswers[$question->id][0])
                                                <div class="d-flex align-items-center scale-container">
                                                    <span class="fw-bold fs-5 me-2">{{ $formattedAnswers[$question->id][0] }}</span>
                                                    <div class="progress flex-grow-1" style="height: 10px;">
                                                        <div class="progress-bar" role="progressbar" 
                                                            style="width: 0%;" 
                                                            aria-valuenow="0" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="10">
                                                        </div>
                                                    </div>
                                                    <span class="ms-2">из 10</span>
                                                </div>
                                            @else
                                                <em class="text-muted">Нет ответа</em>
                                            @endisset
                                        @endif
                                    </div>
                                    
                                    @if($question->type === 'single_choice' || $question->type === 'multiple_choice')
                                        <h6 class="mt-3">Все варианты ответов:</h6>
                                        <div class="ms-3">
                                            @foreach($question->options as $option)
                                                <div class="mb-1">
                                                    @if(isset($formattedAnswers[$question->id]) && 
                                                        (
                                                            ($question->type === 'single_choice' && $formattedAnswers[$question->id][0] === $option) || 
                                                            ($question->type === 'multiple_choice' && in_array($option, $formattedAnswers[$question->id]))
                                                        )
                                                    )
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        <span class="selected-option">{{ $option }}</span>
                                                    @else
                                                        <i class="fas fa-circle text-muted me-1"></i>
                                                        {{ $option }}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="d-flex justify-content-center mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-home me-1"></i> На главную
                            </a>
                            
                            @auth
                                @if(Auth::id() === $survey->user_id)
                                    <a href="{{ route('surveys.results', $survey) }}" class="btn btn-primary">
                                        <i class="fas fa-chart-bar me-2"></i>Посмотреть общие результаты
                                    </a>
                                @endif
                            @endauth


                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Scripts -->
    <script src="{{ asset('public/js/scripts.js') }}"></script>
</body>
</html> 