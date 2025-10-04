<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AnswerController extends Controller
{
    /**
     * Сохранение ответов на опрос
     */
    public function store(Request $request, string $code)
    {
        $survey = Survey::where('code', $code)->firstOrFail();
        
        if (!$survey->isActive()) {
            return redirect()->route('home')
                ->with('error', 'Этот опрос в данный момент недоступен.');
        }
        
        $questions = $survey->questions;
        $validationRules = [];
        $validationMessages = [];
        
        // Формируем правила валидации для каждого вопроса
        foreach ($questions as $question) {
            $fieldName = 'question_' . $question->id;
            
            if ($question->is_required) {
                $validationRules[$fieldName] = 'required';
                $validationMessages[$fieldName . '.required'] = 'Вопрос "' . $question->title . '" обязателен для ответа.';
            } else {
                $validationRules[$fieldName] = 'nullable';
            }
            
            // Дополнительные правила в зависимости от типа вопроса
            switch ($question->type) {
                case Question::TYPE_MULTIPLE_CHOICE:
                    $validationRules[$fieldName] .= '|array';
                    break;
                case Question::TYPE_SCALE:
                    $validationRules[$fieldName] .= '|integer|min:1|max:10';
                    break;
            }
        }
        
        $validator = Validator::make($request->all(), $validationRules, $validationMessages);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Генерируем уникальный ID сессии для анонимных ответов
        $sessionId = Auth::check() ? null : $request->cookie('survey_session_id', Str::uuid());
        $userId = Auth::check() ? Auth::id() : null;
        
        DB::beginTransaction();
        
        try {
            foreach ($questions as $question) {
                $fieldName = 'question_' . $question->id;
                $value = $request->input($fieldName);
                
                // Пропускаем, если нет ответа на необязательный вопрос
                if ($value === null) {
                    continue;
                }
                
                // Преобразуем значение в массив для сохранения
                if (!is_array($value)) {
                    $value = [$value];
                }
                
                Answer::create([
                    'survey_id' => $survey->id,
                    'question_id' => $question->id,
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'value' => $value,
                ]);
            }
            
            // Создаем уведомление для владельца опроса
            $this->createNotification($survey);
            
            DB::commit();
            
            $response = redirect()->route('surveys.thank-you', $survey->code)
                ->with('success', 'Спасибо за ваши ответы!');
                
            // Сохраняем идентификатор сессии в cookie для неавторизованных пользователей
            if (!Auth::check()) {
                $response->cookie('survey_session_id', $sessionId, 60 * 24 * 30); // хранить 30 дней
                
                // Сохраняем информацию о пройденном опросе в cookie
                // Сначала получаем текущий список пройденных опросов из cookie
                $completedSurveys = json_decode($request->cookie('completed_surveys', '[]'), true);
                
                // Добавляем текущий опрос в список, если его там еще нет
                if (!in_array($survey->id, $completedSurveys)) {
                    $completedSurveys[] = $survey->id;
                }
                
                // Сохраняем обновленный список в cookie на 30 дней
                $response->cookie('completed_surveys', json_encode($completedSurveys), 60 * 24 * 30);
            }
            
            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Произошла ошибка при сохранении ответов. Пожалуйста, попробуйте еще раз.')
                ->withInput();
        }
    }
    
    /**
     * Страница благодарности после прохождения опроса
     */
    public function thankYou(string $code)
    {
        $survey = Survey::where('code', $code)->firstOrFail();
        
        return view('surveys.thank-you', compact('survey'));
    }
    
    /**
     * Создание уведомления о новом ответе на опрос
     * 
     * @param Survey $survey Опрос, на который получен ответ
     * @return void
     */
    private function createNotification(Survey $survey): void
    {
        // Получаем владельца опроса
        $owner = $survey->user;
        
        // Создаем уведомление
        Notification::create([
            'user_id' => $owner->id,
            'survey_id' => $survey->id,
            'type' => 'new_response',
            'message' => 'Получен новый ответ на опрос "' . $survey->title . '"',
            'is_read' => false
        ]);
        
        // Проверяем, если это 10-й, 50-й, 100-й или 1000-й ответ, создаем дополнительное уведомление
        $responsesCount = $survey->answers()
            ->select('session_id')
            ->distinct()
            ->count();
            
        if (in_array($responsesCount, [10, 50, 100, 1000])) {
            Notification::create([
                'user_id' => $owner->id,
                'survey_id' => $survey->id,
                'type' => 'milestone',
                'message' => 'Поздравляем! Ваш опрос "' . $survey->title . '" получил ' . $responsesCount . ' ответов!',
                'is_read' => false
            ]);
        }
    }
}
