<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $survey->title }}</title>
    
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
            background-image: url('{{ asset('public/storage/' . $survey->design['background_image']) }}');
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
        
        .progress-bar {
            background-color: var(--primary-color);
        }
        
        .question {
            display: none;
        }
        
        .question.active {
            display: block;
        }
        
        .question-number {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .required-mark {
            color: #dc3545;
        }
        
        .timer-container {
            font-size: 1.2rem;
            color: #6c757d;
        }
        
        .scale-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 1rem 0;
        }
        
        .scale-item {
            flex: 1;
            min-width: 40px;
            text-align: center;
        }
        
        .scale-item input[type="radio"] {
            display: none;
        }
        
        .scale-item label {
            display: block;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .scale-item input[type="radio"]:checked + label {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .scale-item label:hover {
            background-color: #e9ecef;
        }
        
        .scale-item input[type="radio"]:checked + label:hover {
            background-color: var(--primary-color);
        }
        
        .share-container {
            margin-top: 1rem;
        }
        
        .share-buttons-light {
            display: flex;
            gap: 10px;
        }
        
        .share-buttons-light .share-button {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .share-buttons-light .share-button:hover {
            background-color: #e9ecef;
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
                    
                    @if($survey->time_limit)
                        <div class="timer-container mt-3">
                            <i class="fas fa-clock me-2"></i>Оставшееся время: <span class="timer" data-time-limit="{{ $survey->time_limit }}" id="timer">{{ gmdate('i:s', $survey->time_limit) }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="p-4">
                @if($questions->isEmpty())
                    <div class="alert alert-info">
                        В этом опросе пока нет вопросов.
                    </div>
                @else
                    <div class="progress mb-4">
                        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    
                    <form id="survey-form" action="{{ route('surveys.submit', $survey->code) }}" method="POST" data-survey-code="{{ $survey->code }}">
                        @csrf
                        
                        @foreach($questions as $index => $question)
                            <div class="question {{ $index === 0 ? 'active' : '' }}" data-question-id="{{ $question->id }}">
                                <div class="mb-2">
                                    <span class="question-number">Вопрос {{ $index + 1 }} из {{ $questions->count() }}</span>
                                    @if($question->time_limit)
                                        <span class="float-end text-danger fw-bold">
                                            <i class="fas fa-clock me-1"></i><span class="question-timer" data-time="{{ $question->time_limit }}">{{ gmdate('i:s', $question->time_limit) }}</span>
                                        </span>
                                    @endif
                                </div>
                                
                                <h4>
                                    {{ $question->title }}
                                    @if($question->is_required)
                                        <span class="required-mark">*</span>
                                    @endif
                                </h4>
                                
                                @if($question->description)
                                    <p class="text-muted">{{ $question->description }}</p>
                                @endif
                                
                                <div class="mb-4">
                                    @if($question->type === 'single_choice')
                                        @foreach($question->options as $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="question_{{ $question->id }}" id="option_{{ $question->id }}_{{ $loop->index }}" value="{{ $option }}" {{ $question->is_required ? 'required' : '' }}>
                                                <label class="form-check-label" for="option_{{ $question->id }}_{{ $loop->index }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                        
                                    @elseif($question->type === 'multiple_choice')
                                        @foreach($question->options as $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="question_{{ $question->id }}[]" id="option_{{ $question->id }}_{{ $loop->index }}" value="{{ $option }}">
                                                <label class="form-check-label" for="option_{{ $question->id }}_{{ $loop->index }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                        
                                    @elseif($question->type === 'text')
                                        <textarea class="form-control" name="question_{{ $question->id }}" rows="3" {{ $question->is_required ? 'required' : '' }}></textarea>
                                        
                                    @elseif($question->type === 'scale')
                                        <div class="scale-container">
                                            @php
                                                $min = $question->options['min'] ?? 1;
                                                $max = $question->options['max'] ?? 10;
                                            @endphp
                                            <div class="d-flex justify-content-between w-100 mb-2">
                                                <span class="text-muted">Минимум: {{ $min }}</span>
                                                <span class="text-muted">Максимум: {{ $max }}</span>
                                            </div>
                                            @for($i = $min; $i <= $max; $i++)
                                                <div class="scale-item">
                                                    <input type="radio" name="question_{{ $question->id }}" id="scale_{{ $question->id }}_{{ $i }}" value="{{ $i }}" {{ $question->is_required ? 'required' : '' }}>
                                                    <label for="scale_{{ $question->id }}_{{ $i }}">{{ $i }}</label>
                                                </div>
                                            @endfor
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    @if($index > 0)
                                        <button type="button" class="btn btn-outline-secondary prev-btn">
                                            <i class="fas fa-arrow-left me-2"></i>Назад
                                        </button>
                                    @else
                                        <div></div>
                                    @endif
                                    
                                    @if($index < $questions->count() - 1)
                                        <button type="button" class="btn btn-primary next-btn">
                                            Далее<i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-success submit-btn">
                                            <i class="fas fa-check me-2"></i>Завершить
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </form>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script src="{{ asset('public/js/scripts.js') }}"></script>
    <script src="{{ asset('public/js/survey-cache.js') }}"></script>
</body>
</html>
