<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\Answer;
use App\Models\ArchivedSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SurveyController extends Controller
{
    /**
     * Отображение списка опросов пользователя
     */
    public function index()
    {
        // Получаем только неархивированные опросы пользователя
        $surveys = Auth::user()->surveys()
            ->where('is_archived', false) // Исключаем архивированные опросы
            ->latest()
            ->paginate(10);
        
        return view('surveys.index', compact('surveys'));
    }
    
    /**
     * Отображение истории опросов пользователя
     */
    public function history(Request $request)
    {
        $query = Auth::user()->surveys()->where('is_archived', false);
        
        // Применяем фильтры
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'public':
                    $query->where('is_public', true);
                    break;
                case 'private':
                    $query->where('is_public', false);
                    break;
            }
        }
        
        $surveys = $query->latest()->paginate(10);
        
        // Статистика для отображения на странице
        $activeCount = Auth::user()->surveys()->where('is_active', true)->where('is_archived', false)->count();
        
        // Общее количество ответов на все опросы пользователя
        $totalResponses = DB::table('answers')
            ->join('surveys', 'answers.survey_id', '=', 'surveys.id')
            ->where('surveys.user_id', Auth::id())
            ->select('answers.session_id', 'answers.survey_id')
            ->distinct()
            ->count();
        
        // Среднее количество ответов на опрос
        $surveysCount = Auth::user()->surveys()->where('is_archived', false)->count();
        $avgResponsesPerSurvey = $surveysCount > 0 ? round($totalResponses / $surveysCount, 1) : 0;
        
        // Получаем количество архивных опросов
        $archivedCount = Auth::user()->surveys()->where('is_archived', true)->count();
        
        return view('surveys.history', compact('surveys', 'activeCount', 'totalResponses', 'avgResponsesPerSurvey', 'archivedCount'));
    }

    
    /**
     * Удаление архивированного опроса
     */
    public function destroyArchived($id)
    {
        // Получаем архивированный опрос из таблицы surveys
        $archivedSurvey = Survey::where('id', $id)
            ->where('is_archived', true)
            ->firstOrFail();
        
        // Проверка прав доступа
        if (Auth::id() !== $archivedSurvey->user_id) {
            abort(403, 'У вас нет доступа к этому архивному опросу.');
        }
        
        try {
            // Удаляем архивированный опрос и все связанные данные
            $archivedSurvey->delete();
            
            return redirect()->route('surveys.archive.index')
                ->with('success', 'Архивированный опрос успешно удален.');
                
        } catch (\Exception $e) {
            // Если произошла ошибка, возвращаем сообщение об ошибке
            return redirect()->route('surveys.archive.index')
                ->with('error', 'Ошибка при удалении архивированного опроса: ' . $e->getMessage());
        }
    }

    /**
     * Отображение архива опросов
     */
    public function archive()
    {
        // Получаем архивированные опросы из таблицы surveys
        $archivedSurveys = Survey::where('user_id', Auth::id())
            ->where('is_archived', true)
            ->latest('archived_at')
            ->paginate(10);
        
        return view('surveys.archive', compact('archivedSurveys'));
    }
    
    /**
     * Просмотр архивного опроса
     */
    public function showArchived($id)
    {
        // Получаем архивированный опрос из таблицы surveys
        $archivedSurvey = Survey::where('id', $id)
            ->where('is_archived', true)
            ->firstOrFail();
        
        // Проверка прав доступа
        if (Auth::id() !== $archivedSurvey->user_id) {
            abort(403, 'У вас нет доступа к этому архивному опросу.');
        }
        
        // Получаем вопросы и ответы
        // Теперь мы можем получить их напрямую из отношений модели
        $questions = $archivedSurvey->questions()->orderBy('position')->get();
        $answers = $archivedSurvey->answers()->get();
            
        // Группируем ответы по вопросам
        $questionData = [];
        foreach ($questions as $question) {
            $questionAnswers = $answers->where('question_id', $question->id);
            
            if ($question->type === 'single_choice' || $question->type === 'multiple_choice') {
                $options = array_fill_keys($question->options, 0);
                $total = 0;
                
                foreach ($questionAnswers as $answer) {
                    $values = is_array($answer->value) ? $answer->value : [$answer->value];
                    foreach ($values as $value) {
                        if (isset($options[$value])) {
                            $options[$value]++;
                            $total++;
                        }
                    }
                }
                
                $questionData[$question->id] = [
                    'options' => $options,
                    'total' => $total
                ];
            } elseif ($question->type === 'scale') {
                $distribution = array_fill(1, 10, 0);
                $values = [];
                
                foreach ($questionAnswers as $answer) {
                    $value = $answer->value;
                    if (is_numeric($value)) {
                        $value = (int)$value;
                        if ($value >= 1 && $value <= 10) {
                            $distribution[$value]++;
                            $values[] = $value;
                        }
                    }
                }
                
                $questionData[$question->id] = [
                    'distribution' => $distribution,
                    'average' => count($values) > 0 ? round(array_sum($values) / count($values), 1) : 0,
                    'total' => count($values)
                ];
            } else {
                $textAnswers = [];
                foreach ($questionAnswers as $answer) {
                    $textAnswers[] = [
                        'text' => $answer->value,
                        'date' => $answer->created_at->format('d.m.Y H:i')
                    ];
                }
                
                $questionData[$question->id] = [
                    'answers' => $textAnswers
                ];
            }
        }
        
        // Статистика
        $totalResponses = $answers->pluck('session_id')->unique()->count();
        
        return view('surveys.archived-show', compact('archivedSurvey', 'questions', 'questionData', 'totalResponses'));
    }

    /**
     * Форма создания нового опроса
     */
    public function create()
    {
        return view('surveys.create');
    }

    /**
     * Сохранение нового опроса
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'access_code' => 'nullable|string|max:50',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'time_limit' => 'nullable|integer|min:1',
            'design' => 'nullable|array',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Если опрос публичный, очищаем код доступа
        if (!empty($validated['is_public'])) {
            $validated['access_code'] = null;
        }

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['code'] = Str::random(8);
        
        // Миграция с background_opacity на image_opacity, если необходимо
        if (isset($validated['design']['background_opacity']) && !isset($validated['design']['image_opacity'])) {
            $validated['design']['image_opacity'] = $validated['design']['background_opacity'];
            unset($validated['design']['background_opacity']);
        }
        
        // Обработка загруженного фонового изображения
        if ($request->hasFile('background_image')) {
            $backgroundImage = $request->file('background_image')->store('survey_backgrounds', 'public');
            $validated['design']['background_image'] = $backgroundImage;
        }

        $survey = Survey::create($validated);

        return redirect()->route('surveys.questions.create', $survey)
            ->with('success', 'Опрос успешно создан! Теперь добавьте вопросы.');
    }

    /**
     * Отображение опроса
     */
    public function show(Survey $survey)
    {
        Gate::authorize('view', $survey);
        
        $questions = $survey->questions;
        $responsesCount = DB::table('answers')
            ->where('survey_id', $survey->id)
            ->select('session_id')
            ->distinct()
            ->count();
            
        // Генерация QR-кода для опроса
        $qrCodeUrl = route('surveys.take', $survey->code);
        
        return view('surveys.show', compact('survey', 'questions', 'responsesCount', 'qrCodeUrl'));
    }

    /**
     * Форма редактирования опроса
     */
    public function edit(Survey $survey)
    {
        Gate::authorize('update', $survey);
        
        return view('surveys.edit', compact('survey'));
    }

    /**
     * Обновление опроса
     */
    public function update(Request $request, Survey $survey)
    {
        Gate::authorize('update', $survey);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'access_code' => 'nullable|string|max:50',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'time_limit' => 'nullable|integer|min:1',
            'design' => 'nullable|array',
            'is_active' => 'boolean',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_background_image' => 'nullable|boolean',
        ]);
        
        // Если опрос публичный, очищаем код доступа
        if (!empty($validated['is_public'])) {
            $validated['access_code'] = null;
        }
        
        // Обработка дизайна
        $design = $survey->design ?? [];
        
        if (isset($validated['design'])) {
            // Миграция с background_opacity на image_opacity, если необходимо
            if (isset($design['background_opacity']) && !isset($validated['design']['image_opacity'])) {
                $validated['design']['image_opacity'] = $design['background_opacity'];
                unset($design['background_opacity']);
            }
            
            $design = array_merge($design, $validated['design']);
        }
        
        // Обработка загруженного фонового изображения
        if ($request->hasFile('background_image')) {
            // Удаляем старое изображение, если оно существует
            if (!empty($survey->design['background_image'])) {
                Storage::disk('public')->delete($survey->design['background_image']);
            }
            
            $backgroundImage = $request->file('background_image')->store('survey_backgrounds', 'public');
            $design['background_image'] = $backgroundImage;
        }
        
        // Обработка удаления фонового изображения
        if ($request->has('remove_background_image') && $request->remove_background_image) {
            // Удаляем изображение из хранилища, если оно существует
            if (!empty($survey->design['background_image'])) {
                Storage::disk('public')->delete($survey->design['background_image']);
            }
            
            // Удаляем информацию об изображении из настроек дизайна
            if (isset($design['background_image'])) {
                unset($design['background_image']);
            }
        }
        
        $validated['design'] = $design;
        
        $survey->update($validated);

        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Опрос успешно обновлен!');
    }

    /**
     * Архивирование опроса
     */
    public function archiveSurvey(Survey $survey)
    {
        Gate::authorize('delete', $survey);
        
        // Сохраняем информацию об опросе для отладки
        $surveyId = $survey->id;
        $userId = $survey->user_id;
        $surveyTitle = $survey->title;
        
        try {
            // Создаем архивную копию опроса
            $archivedSurvey = new ArchivedSurvey();
            $archivedSurvey->original_id = $surveyId;
            $archivedSurvey->user_id = $userId;
            $archivedSurvey->title = $surveyTitle;
            $archivedSurvey->description = $survey->description;
            $archivedSurvey->slug = $survey->slug;
            
            // Генерируем уникальный код для архивной копии
            // Это решает проблему с дублированием кодов при повторном архивировании
            $uniqueCode = Str::random(8);
            
            $archivedSurvey->code = $uniqueCode;
            $archivedSurvey->access_code = $survey->access_code;
            $archivedSurvey->design = $survey->design;
            $archivedSurvey->is_public = $survey->is_public;
            $archivedSurvey->is_active = $survey->is_active;
            $archivedSurvey->show_results = $survey->show_results ?? true;
            $archivedSurvey->start_at = $survey->start_at;
            $archivedSurvey->end_at = $survey->end_at;
            $archivedSurvey->time_limit = $survey->time_limit;
            $archivedSurvey->views = $survey->views ?? 0;
            $archivedSurvey->archived_at = now();
            $archivedSurvey->save();
            
            // Вместо удаления опроса, помечаем его как архивированный и неактивный
            // Это позволит сохранить все связанные данные (вопросы и ответы)
            // При этом опрос становится недоступным для прохождения
            $survey->is_archived = true;
            $survey->is_active = false; // Делаем опрос неактивным
            $survey->archived_at = now();
            $survey->save();
            
            // Добавляем информацию об успешном архивировании
            $message = 'Опрос "' . $surveyTitle . '" успешно архивирован';
            
            // Логирование архивирования опроса
            ActivityLogService::log(
                'archive',
                'Survey',
                $survey->id,
                'Архивирование опроса: ' . $surveyTitle,
                $survey->toArray(),
                ['archived_id' => $archivedSurvey->id, 'archived_at' => now()->toDateTimeString()]
            );
            
            return redirect()->route('surveys.archive.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            // Если произошла ошибка, возвращаем сообщение об ошибке
            return redirect()->route('surveys.index')
                ->with('error', 'Ошибка при архивировании опроса: ' . $e->getMessage());
        }
    }

    /**
     * Полное удаление опроса
     */
    public function destroy(Survey $survey)
    {
        Gate::authorize('delete', $survey);
        
        // Сохраняем информацию об опросе для логирования
        $surveyId = $survey->id;
        $surveyTitle = $survey->title;
        
        try {
            // Логирование перед удалением
            ActivityLogService::log(
                'delete',
                'Survey',
                $survey->id,
                'Полное удаление опроса: ' . $surveyTitle,
                $survey->toArray(),
                ['deleted_at' => now()->toDateTimeString()]
            );
            
            // Полное удаление опроса и всех связанных данных
            $survey->delete();
            
            return redirect()->route('surveys.index')
                ->with('success', 'Опрос "' . $surveyTitle . '" успешно удален');
                
        } catch (\Exception $e) {
            // Если произошла ошибка, возвращаем сообщение об ошибке
            return redirect()->route('surveys.index')
                ->with('error', 'Ошибка при удалении опроса: ' . $e->getMessage());
        }
    }

    /**
     * Страница для прохождения опроса
     * 
     * @param string $code Код опроса
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function take(string $code, Request $request)
    {
        $survey = Survey::where('code', $code)->firstOrFail();
        
        // Проверяем, активен ли опрос и не архивирован ли он
        if (!$survey->is_active || $survey->is_archived) {
            return redirect()->route('home')
                ->with('error', 'Этот опрос недоступен для прохождения.');
        }
        
        // Проверяем публичность опроса для неавторизованных пользователей
        if (!Auth::check() && !$survey->is_public) {
            return redirect()->route('login')
                ->with('error', 'Для прохождения этого опроса необходимо авторизоваться.');
        }
        
        // Проверяем, не проходил ли уже пользователь этот опрос
        $hasAnswered = false;
        
        if (Auth::check()) {
            // Для авторизованных пользователей проверяем по user_id
            $hasAnswered = Answer::where('survey_id', $survey->id)
                ->where('user_id', Auth::id())
                ->exists();
        } else {
            // Для неавторизованных пользователей проверяем по session_id из cookie
            $sessionId = $request->cookie('survey_session_id');
            
            if ($sessionId) {
                $hasAnswered = Answer::where('survey_id', $survey->id)
                    ->where('session_id', $sessionId)
                    ->exists();
            }
        }
                
        if ($hasAnswered) {
            // Вместо ошибки перенаправляем на просмотр ответов
            return redirect()->route('surveys.view-responses', $survey->code);
        }
        
        // Проверяем временные ограничения
        if ($survey->start_at && now()->lt($survey->start_at)) {
            return redirect()->route('home')
                ->with('error', 'Этот опрос еще не начался. Начало: ' . $survey->start_at->format('d.m.Y H:i'));
        }
        
        if ($survey->end_at && now()->gt($survey->end_at)) {
            return redirect()->route('home')
                ->with('error', 'Этот опрос уже завершен. Окончание: ' . $survey->end_at->format('d.m.Y H:i'));
        }
        
        // Проверяем ограничения доступа
        if ($survey->access_code && request()->get('access_code') !== $survey->access_code) {
            return view('surveys.access', compact('survey'));
        }
        
        // Увеличиваем счетчик просмотров
        $survey->increment('views');
        
        // Получаем вопросы опроса
        $questions = $survey->questions()->orderBy('position')->get();
        
        return view('surveys.take', compact('survey', 'questions'));
    }
    
    /**
     * Отображение результатов опроса
     */
    public function results(Survey $survey)
    {
        Gate::authorize('view', $survey);
        
        // Предварительно загружаем отношение answers для всех вопросов
        $questions = $survey->questions()->with('answers')->get();
        $questionResults = [];
        
        // Подготовка данных для статистики
        // Количество уникальных респондентов (людей, которые прошли опрос)
        $totalRespondents = DB::table('answers')
            ->where('survey_id', $survey->id)
            ->select('session_id', 'user_id')
            ->distinct()
            ->count();

        // Общее количество ответов на все вопросы
        $totalResponses = DB::table('answers')
            ->where('survey_id', $survey->id)
            ->count();

        // Процент завершения (процент людей, которые начали и завершили опрос)
// Ограничиваем максимальное значение до 100%
$completionRate = $survey->views > 0 ? min(100, round(($totalRespondents / $survey->views) * 100)) : 0;
        
        // Среднее время прохождения (заглушка)
        $averageTime = '2 мин';
        
        // Последний ответ
        $lastResponse = DB::table('answers')
            ->where('survey_id', $survey->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $lastResponseDate = $lastResponse ? date('d.m.Y', strtotime($lastResponse->created_at)) : 'нет ответов';
        
        // Данные для графика активности
        $activityData = [
            'labels' => [],
            'data' => []
        ];
        
        // Получаем данные за последние 7 дней
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $activityData['labels'][] = now()->subDays($i)->format('d.m');
            
            $count = DB::table('answers')
                ->where('survey_id', $survey->id)
                ->whereDate('created_at', $date)
                ->select('session_id', 'user_id')
                ->distinct()
                ->count();
            
            $activityData['data'][] = $count;
        }
        
        // Подготовка данных по вопросам
        foreach ($questions as $question) {
            $answers = $question->answers;
            
            if ($question->type === 'single_choice' || $question->type === 'multiple_choice') {
                // Подсчет для вопросов с выбором
                $options = array_fill_keys($question->options, 0);
                $total = 0;
                
                foreach ($answers as $answer) {
                    $values = $answer->value;
                    if (!is_array($values)) {
                        $values = [$values];
                    }
                    
                    foreach ($values as $value) {
                        if (isset($options[$value])) {
                            $options[$value]++;
                            $total++;
                        }
                    }
                }
                
                $questionResults[$question->id] = [
                    'options' => $options,
                    'total' => $total
                ];
            } elseif ($question->type === 'scale') {
                // Подсчет для шкалы
                $distribution = array_fill(1, 10, 0);
                $values = [];
                $total = 0;
                
                foreach ($answers as $answer) {
                    $value = $answer->value[0] ?? null;
                    if ($value !== null && is_numeric($value)) {
                        $value = (int) $value;
                        if ($value >= 1 && $value <= 10) {
                            $distribution[$value]++;
                            $values[] = $value;
                            $total++;
                        }
                    }
                }
                
                $questionResults[$question->id] = [
                    'distribution' => $distribution,
                    'average' => $total > 0 ? round(array_sum($values) / $total, 1) : 0,
                    'min' => $total > 0 ? min($values) : 0,
                    'max' => $total > 0 ? max($values) : 0,
                    'total' => $total
                ];
            } elseif ($question->type === 'text') {
                // Для текстовых ответов
                $textAnswers = [];
                
                foreach ($answers as $answer) {
                    $textAnswers[] = [
                        'text' => $answer->value[0] ?? '',
                        'date' => date('d.m.Y H:i', strtotime($answer->created_at))
                    ];
                }
                
                $questionResults[$question->id] = [
                    'answers' => $textAnswers
                ];
            }
        }
        
        return view('surveys.results', compact(
            'survey', 
            'questions', 
            'questionResults', 
            'totalResponses',
            'totalRespondents', 
            'completionRate', 
            'averageTime', 
            'lastResponseDate',
            'activityData'
        ));
    }
    
    /**
     * Экспорт результатов опроса в различных форматах
     * 
     * @param Survey $survey
     * @param string $format Формат экспорта (csv, pdf)
     * @return \Illuminate\Http\Response
     */
    public function export(Survey $survey, $format = 'csv')
    {
        Gate::authorize('view', $survey);
        
        $questions = $survey->questions;
        
        // Подготовка данных для экспорта
        $data = $this->prepareExportData($survey, $questions);
        
        // Экспорт в зависимости от формата
        switch ($format) {
            case 'pdf':
                return $this->exportPdf($survey, $questions, $data);
            case 'csv':
            default:
                return $this->exportCsv($survey, $questions, $data);
        }
    }
    
    /**
     * Подготовка данных для экспорта
     */
    private function prepareExportData(Survey $survey, $questions)
    {
        // Получаем все уникальные сессии/пользователей
        $sessions = DB::table('answers')
            ->where('survey_id', $survey->id)
            ->select('session_id', 'user_id', 'created_at')
            ->distinct()
            ->orderBy('created_at')
            ->get();
        
        $data = [];
        
        foreach ($sessions as $session) {
            $row = [
                'id' => $session->session_id ?? $session->user_id,
                'created_at' => $session->created_at
            ];
            
            $answers = [];
            
            foreach ($questions as $question) {
                $answer = Answer::where('survey_id', $survey->id)
                    ->where('question_id', $question->id)
                    ->where(function($query) use ($session) {
                        if ($session->session_id) {
                            $query->where('session_id', $session->session_id);
                        } else {
                            $query->where('user_id', $session->user_id);
                        }
                    })
                    ->first();
                
                if ($answer) {
                    if (is_array($answer->value)) {
                        $answers[$question->id] = implode(', ', $answer->value);
                    } else {
                        $answers[$question->id] = $answer->value;
                    }
                } else {
                    $answers[$question->id] = '';
                }
            }
            
            $row['answers'] = $answers;
            $data[] = $row;
        }
        
        return $data;
    }
    
    /**
     * Экспорт результатов опроса в CSV
     */
    private function exportCsv(Survey $survey, $questions, $data)
    {
        $filename = Str::slug($survey->title) . '-results.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($survey, $questions, $data) {
            $file = fopen('php://output', 'w');
            
            // Заголовки CSV
            $header = ['ID', 'Время ответа'];
            foreach ($questions as $question) {
                $header[] = $question->title;
            }
            fputcsv($file, $header);
            
            // Данные
            foreach ($data as $row) {
                $csvRow = [
                    $row['id'],
                    $row['created_at']
                ];
                
                foreach ($questions as $question) {
                    $csvRow[] = $row['answers'][$question->id] ?? '';
                }
                
                fputcsv($file, $csvRow);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Экспорт результатов опроса в PDF
     */
    private function exportPdf(Survey $survey, $questions, $data)
    {
        // Подготовка данных для PDF
        $totalResponses = count($data);
        $completionRate = $totalResponses > 0 ? round(($totalResponses / $survey->views) * 100) : 0;
        
        // Средняя продолжительность прохождения опроса
        $averageTime = '2 мин'; // Заглушка, в реальном приложении нужно рассчитывать
        
        // Дата последнего ответа
        $lastResponseDate = $totalResponses > 0 ? 
            date('d.m.Y', strtotime($data[count($data) - 1]['created_at'])) : 
            'нет ответов';
        
        // Подготовка данных по вопросам
        $questionResults = [];
        
        foreach ($questions as $question) {
            $result = [];
            
            if ($question->type === 'single_choice' || $question->type === 'multiple_choice') {
                $options = array_fill_keys($question->options, 0);
                $total = 0;
                
                foreach ($data as $row) {
                    $answer = $row['answers'][$question->id] ?? '';
                    if (!empty($answer)) {
                        $answerOptions = explode(', ', $answer);
                        foreach ($answerOptions as $option) {
                            if (isset($options[$option])) {
                                $options[$option]++;
                                $total++;
                            }
                        }
                    }
                }
                
                $result['options'] = $options;
                $result['total'] = $total;
            } elseif ($question->type === 'scale') {
                $distribution = array_fill(1, 10, 0);
                $values = [];
                $total = 0;
                
                foreach ($data as $row) {
                    $answer = $row['answers'][$question->id] ?? '';
                    if (!empty($answer) && is_numeric($answer)) {
                        $value = (int) $answer;
                        if ($value >= 1 && $value <= 10) {
                            $distribution[$value]++;
                            $values[] = $value;
                            $total++;
                        }
                    }
                }
                
                $result['distribution'] = $distribution;
                $result['average'] = $total > 0 ? round(array_sum($values) / $total, 1) : 0;
                $result['min'] = $total > 0 ? min($values) : 0;
                $result['max'] = $total > 0 ? max($values) : 0;
                $result['total'] = $total;
            } elseif ($question->type === 'text') {
                $answers = [];
                
                foreach ($data as $row) {
                    $answer = $row['answers'][$question->id] ?? '';
                    if (!empty($answer)) {
                        $answers[] = [
                            'text' => $answer,
                            'date' => date('d.m.Y H:i', strtotime($row['created_at']))
                        ];
                    }
                }
                
                $result['answers'] = $answers;
            }
            
            $questionResults[$question->id] = $result;
        }
        
        // Генерация PDF с помощью пакета dompdf
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('surveys.export.pdf', [
            'survey' => $survey,
            'questions' => $questions,
            'questionResults' => $questionResults,
            'totalResponses' => $totalResponses,
            'completionRate' => $completionRate,
            'averageTime' => $averageTime,
            'lastResponseDate' => $lastResponseDate
        ]);
        
        return $pdf->download(Str::slug($survey->title) . '-results.pdf');
    }
    
    /**
     * Отображение QR-кода для опроса
     */
    public function qrcode(Survey $survey)
    {
        Gate::authorize('view', $survey);
        
        // Используем Google Charts API для генерации QR-кода
        $url = route('surveys.take', $survey->code);
        $qrCodeUrl = 'https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=' . urlencode($url) . '&choe=UTF-8';
        
        // Проверяем доступность QR-кода
        $headers = @get_headers($qrCodeUrl);
        if (!$headers || strpos($headers[0], '200') === false) {
            // Если Google Charts API недоступен, используем альтернативный сервис
            $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($url);
        }
        
        return view('surveys.qrcode', compact('survey', 'qrCodeUrl'));
    }
    
    /**
     * Отображение публичных результатов опроса
     */
    public function publicResults($code)
    {
        $survey = Survey::where('code', $code)->firstOrFail();
        
        // Проверяем, что опрос не архивирован
        if ($survey->is_archived) {
            return redirect()->route('home')
                ->with('error', 'Результаты архивированного опроса недоступны для публичного просмотра.');
        }
        
        // Проверяем наличие поля show_results в модели
        if (array_key_exists('show_results', $survey->getAttributes()) && !$survey->show_results) {
            return redirect()->route('surveys.take', $code)
                ->with('error', 'Результаты этого опроса не доступны для публичного просмотра.');
        }
        
        $questions = $survey->questions;
        $questionResults = [];
        
        // Подготовка данных для статистики
        $totalResponses = DB::table('answers')
            ->where('survey_id', $survey->id)
            ->select('session_id', 'user_id')
            ->distinct()
            ->count();
        
        $completionRate = $survey->views > 0 ? round(($totalResponses / $survey->views) * 100) : 0;
        
        // Среднее время прохождения (заглушка)
        $averageTime = '2 мин';
        
        // Последний ответ
        $lastResponse = DB::table('answers')
            ->where('survey_id', $survey->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $lastResponseDate = $lastResponse ? date('d.m.Y', strtotime($lastResponse->created_at)) : 'нет ответов';
        
        // Данные для графика активности
        $activityData = [
            'labels' => [],
            'data' => []
        ];
        
        // Получаем данные за последние 7 дней
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $activityData['labels'][] = now()->subDays($i)->format('d.m');
            
            $count = DB::table('answers')
                ->where('survey_id', $survey->id)
                ->whereDate('created_at', $date)
                ->select('session_id', 'user_id')
                ->distinct()
                ->count();
            
            $activityData['data'][] = $count;
        }
        
        // Подготовка данных по вопросам
        foreach ($questions as $question) {
            $answers = $question->answers;
            
            if ($question->type === 'single_choice' || $question->type === 'multiple_choice') {
                // Подсчет для вопросов с выбором
                $options = array_fill_keys($question->options, 0);
                $total = 0;
                
                foreach ($answers as $answer) {
                    $values = $answer->value;
                    if (!is_array($values)) {
                        $values = [$values];
                    }
                    
                    foreach ($values as $value) {
                        if (isset($options[$value])) {
                            $options[$value]++;
                            $total++;
                        }
                    }
                }
                
                $questionResults[$question->id] = [
                    'options' => $options,
                    'total' => $total
                ];
            } elseif ($question->type === 'scale') {
                // Подсчет для шкалы
                $distribution = array_fill(1, 10, 0);
                $values = [];
                $total = 0;
                
                foreach ($answers as $answer) {
                    $value = $answer->value[0] ?? null;
                    if ($value !== null && is_numeric($value)) {
                        $value = (int) $value;
                        if ($value >= 1 && $value <= 10) {
                            $distribution[$value]++;
                            $values[] = $value;
                            $total++;
                        }
                    }
                }
                
                $questionResults[$question->id] = [
                    'distribution' => $distribution,
                    'average' => $total > 0 ? round(array_sum($values) / $total, 1) : 0,
                    'min' => $total > 0 ? min($values) : 0,
                    'max' => $total > 0 ? max($values) : 0,
                    'total' => $total
                ];
            } elseif ($question->type === 'text') {
                // Для текстовых ответов
                $textAnswers = [];
                
                foreach ($answers as $answer) {
                    $textAnswers[] = [
                        'text' => $answer->value[0] ?? '',
                        'date' => date('d.m.Y H:i', strtotime($answer->created_at))
                    ];
                }
                
                $questionResults[$question->id] = [
                    'answers' => $textAnswers
                ];
            }
        }
        
        return view('surveys.results', compact(
            'survey', 
            'questions', 
            'questionResults', 
            'totalResponses', 
            'completionRate', 
            'averageTime', 
            'lastResponseDate',
            'activityData'
        ));
    }
    
    /**
     * Отображение ранее данных пользователем ответов
     * 
     * @param string $code Код опроса
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function viewResponses($code, Request $request)
    {
        $survey = Survey::where('code', $code)->firstOrFail();
        
        // Получаем ответы пользователя на опрос
        $userAnswers = collect();
        
        if (Auth::check()) {
            // Для авторизованных пользователей ищем по user_id
            $userAnswers = Answer::where('survey_id', $survey->id)
                ->where('user_id', Auth::id())
                ->get();
        } else {
            // Для неавторизованных пользователей ищем по session_id из cookie
            $sessionId = $request->cookie('survey_session_id');
            
            if ($sessionId) {
                $userAnswers = Answer::where('survey_id', $survey->id)
                    ->where('session_id', $sessionId)
                    ->get();
            }
        }
            
        if ($userAnswers->isEmpty()) {
            return redirect()->route('surveys.take', $code)
                ->with('error', 'Вы еще не проходили этот опрос или ваша сессия истекла.');
        }
        
        // Преобразуем ответы в удобный формат для отображения
        $formattedAnswers = [];
        
        foreach ($userAnswers as $answer) {
            $formattedAnswers[$answer->question_id] = $answer->value;
        }
        
        // Получаем вопросы опроса
        $questions = $survey->questions()->orderBy('position')->get();
        
        return view('surveys.view-responses', compact('survey', 'questions', 'formattedAnswers'));
    }

}
