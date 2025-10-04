<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты опроса: {{ $survey->title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .container {
            width: 100%;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .header p {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }
        
        .summary {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .summary-item {
            flex: 1;
            min-width: 120px;
            margin: 0 10px 10px;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 5px;
            text-align: center;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #4e73df;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
        }
        
        .question {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        
        .question-header {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        
        .question-type {
            font-size: 11px;
            color: #666;
            font-weight: normal;
            margin-left: 10px;
        }
        
        .chart-container {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .chart-image {
            max-width: 100%;
            height: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th, table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .text-responses {
            margin-bottom: 20px;
        }
        
        .text-response {
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .text-response-meta {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Результаты опроса: {{ $survey->title }}</h1>
            <p>{{ $survey->description }}</p>
            <p>Дата создания: {{ $survey->created_at->format('d.m.Y') }}</p>
            <p>Дата экспорта: {{ now()->format('d.m.Y H:i') }}</p>
        </div>
        
        <div class="summary">
            <div class="summary-title">Общая статистика</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value">{{ $totalResponses }}</div>
                    <div class="summary-label">Всего ответов</div>
                </div>

                <div class="summary-item">
                    <div class="summary-value">{{ $averageTime }}</div>
                    <div class="summary-label">Среднее время</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">{{ $lastResponseDate }}</div>
                    <div class="summary-label">Последний ответ</div>
                </div>
            </div>
        </div>
        
        @foreach($questions as $question)
            <div class="question">
                <div class="question-header">
                    {{ $loop->iteration }}. {{ $question->title }}
                    <span class="question-type">
                        @switch($question->type)
                            @case('single_choice')
                                Одиночный выбор
                                @break
                            @case('multiple_choice')
                                Множественный выбор
                                @break
                            @case('text')
                                Текстовый ответ
                                @break
                            @case('scale')
                                Шкала
                                @break
                        @endswitch
                    </span>
                </div>
                
                @if($question->type === 'single_choice' || $question->type === 'multiple_choice')
                    @if(isset($chartImages[$question->id]))
                        <div class="chart-container">
                            <img src="{{ $chartImages[$question->id] }}" alt="График для вопроса {{ $question->title }}" class="chart-image">
                        </div>
                    @endif
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Вариант</th>
                                <th>Количество</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questionResults[$question->id]['options'] as $option => $count)
                                <tr>
                                    <td>{{ $option }}</td>
                                    <td>{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($question->type === 'scale')
                    @if(isset($chartImages[$question->id]))
                        <div class="chart-container">
                            <img src="{{ $chartImages[$question->id] }}" alt="График для вопроса {{ $question->title }}" class="chart-image">
                        </div>
                    @endif
                    
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="summary-value">{{ $questionResults[$question->id]['average'] }}</div>
                            <div class="summary-label">Средняя оценка</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">{{ $questionResults[$question->id]['min'] }}</div>
                            <div class="summary-label">Минимум</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">{{ $questionResults[$question->id]['max'] }}</div>
                            <div class="summary-label">Максимум</div>
                        </div>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Оценка</th>
                                <th>Количество</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questionResults[$question->id]['distribution'] as $rating => $count)
                                <tr>
                                    <td>{{ $rating }}</td>
                                    <td>{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($question->type === 'text')
                    <div class="text-responses">
                        @if(count($questionResults[$question->id]['answers']) > 0)
                            @foreach($questionResults[$question->id]['answers'] as $index => $answer)
                                <div class="text-response">
                                    {{ $answer['text'] }}
                                    <div class="text-response-meta">
                                        Ответ #{{ $index + 1 }} | {{ $answer['date'] }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Нет текстовых ответов.</p>
                        @endif
                    </div>
                @endif
            </div>
            
            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
        
        <div class="footer">
            <p>Документ сгенерирован автоматически {{ now()->format('d.m.Y H:i') }}</p>
            <p>© {{ date('Y') }} Сервис опросов</p>
        </div>
    </div>
</body>
</html>
