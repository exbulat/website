<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Survey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogService;

class AdminController extends Controller
{
    /**
     * Создание нового экземпляра контроллера
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }

    /**
     * Отображение панели администратора
     */
    public function index()
    {
        // Общая статистика
        $totalUsers = User::count();
        $totalSurveys = Survey::count();
        $totalResponses = DB::table('answers')
            ->select('session_id', 'user_id')
            ->distinct()
            ->count();
        
        // Новые пользователи за последние 7 дней
        $newUsers = User::where('created_at', '>=', now()->subDays(7))->count();
        
        // Новые опросы за последние 7 дней
        $newSurveys = Survey::where('created_at', '>=', now()->subDays(7))->count();
        
        // Статистика по активности (опросы за последние 30 дней)
        $activityData = [
            'labels' => [],
            'data' => []
        ];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $activityData['labels'][] = now()->subDays($i)->format('d.m');
            
            $count = DB::table('answers')
                ->whereDate('created_at', $date)
                ->select('session_id', 'user_id')
                ->distinct()
                ->count();
            
            $activityData['data'][] = $count;
        }
        
        // Статистика по типам опросов
        $surveyTypes = DB::table('surveys')
            ->join('questions', 'surveys.id', '=', 'questions.survey_id')
            ->select('questions.type', DB::raw('count(*) as total'))
            ->groupBy('questions.type')
            ->get()
            ->pluck('total', 'type')
            ->toArray();
            
        // Топ-5 активных пользователей
        $topUsers = DB::table('users')
            ->leftJoin('surveys', 'users.id', '=', 'surveys.user_id')
            ->select('users.id', 'users.name', 'users.email', DB::raw('count(surveys.id) as survey_count'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('survey_count', 'desc')
            ->limit(5)
            ->get();
            
         // Топ-5 популярных опросов
        $topSurveys = DB::table('surveys')
            ->leftJoin('answers', 'surveys.id', '=', 'answers.survey_id')
            ->select(
                'surveys.id', 
                'surveys.title', 
                DB::raw('count(DISTINCT answers.session_id) as response_count'),
                DB::raw('count(DISTINCT CONCAT(answers.session_id, "-", answers.survey_id)) as completed_count')
            )
            ->groupBy('surveys.id', 'surveys.title')
            ->orderBy('response_count', 'desc')
            ->limit(5)
            ->get();
            
        // Преобразуем объекты в модели для удобства работы в шаблоне
        $topSurveys = $topSurveys->map(function($survey) {
            $surveyModel = Survey::find($survey->id);
            if ($surveyModel) {
                // Количество уникальных сессий, которые ответили на опрос
                $surveyModel->response_count = $survey->response_count;
                // Количество завершенных прохождений опроса
                $surveyModel->completed_count = $survey->completed_count;
                
                // Дополнительно получаем количество вопросов в опросе
                $surveyModel->questions_count = $surveyModel->questions()->count();
            }
            return $surveyModel;
        })->filter();
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalSurveys', 
            'totalResponses', 
            'newUsers', 
            'newSurveys', 
            'activityData',
            'surveyTypes',
            'topUsers',
            'topSurveys'
        ));
    }
    
    /**
     * Отображение списка пользователей
     */
    public function users(Request $request)
    {
        $query = User::query();
        
        // Фильтрация и поиск
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('role')) {
            $role = $request->input('role');
            if ($role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($role === 'user') {
                $query->where('is_admin', false);
            }
        }
        
        // Сортировка
        $sortField = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortField, $sortOrder);
        
        $users = $query->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }
    
    /**
     * Форма для создания нового пользователя
     */
    public function createUser()
    {
        return view('admin.users.create');
    }
    
    /**
     * Сохранение нового пользователя
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean'
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin') ? true : false,
            'is_super_admin' => $request->has('is_super_admin') ? true : false
        ]);
        
        // Логирование создания пользователя
        ActivityLogService::logCreated(
            'User',
            $user->id,
            $user->toArray(),
            'Создание нового пользователя: ' . $user->name
        );
        
        return redirect()->route('admin.users')
            ->with('success', 'Пользователь успешно создан!');
    }
    
    /**
     * Форма для редактирования пользователя
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }
    
    /**
     * Обновление данных пользователя
     */
    public function updateUser(Request $request, User $user)
    {
        // Проверка, чтобы обычный администратор не мог изменять записи суперадмина
        if ($user->is_super_admin && !auth()->user()->is_super_admin) {
            return redirect()->route('admin.users')
                ->with('error', 'Вы не можете изменять данные суперадминистратора!');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean'
        ]);
        
        // Сохраняем старые значения для логирования
        $oldValues = $user->toArray();
        
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->has('is_admin') ? true : false,
            'is_super_admin' => $request->has('is_super_admin') ? true : false
        ];
        
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $user->update($userData);
        
        // Логирование обновления пользователя
        ActivityLogService::logUpdated(
            'User',
            $user->id,
            $oldValues,
            $user->toArray(),
            'Обновление пользователя: ' . $user->name
        );
        
        // Если изменилась роль пользователя, логируем это отдельно
        if ($oldValues['is_admin'] != $user->is_admin || 
            (isset($oldValues['is_super_admin']) && $oldValues['is_super_admin'] != $user->is_super_admin)) {
            ActivityLogService::logRoleChange(
                $user->id,
                ['is_admin' => $oldValues['is_admin'], 'is_super_admin' => $oldValues['is_super_admin'] ?? false],
                ['is_admin' => $user->is_admin, 'is_super_admin' => $user->is_super_admin]
            );
        }
        
        return redirect()->route('admin.users')
            ->with('success', 'Данные пользователя обновлены!');
    }
    
    /**
     * Удаление пользователя
     */
    public function destroyUser(User $user)
    {
        // Проверка, чтобы админ не удалил сам себя
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users')
                ->with('error', 'Вы не можете удалить свою учетную запись!');
        }
        
        // Проверка, чтобы обычный администратор не мог удалить суперадмина
        if ($user->is_super_admin && !auth()->user()->is_super_admin) {
            return redirect()->route('admin.users')
                ->with('error', 'Вы не можете удалить суперадминистратора!');
        }
        
        // Сохраняем данные пользователя для логирования
        $userData = $user->toArray();
        $userName = $user->name;
        $userId = $user->id;
        
        $user->delete();
        
        // Логирование удаления пользователя
        ActivityLogService::logDeleted(
            'User',
            $userId,
            $userData,
            'Удаление пользователя: ' . $userName
        );
        
        return redirect()->route('admin.users')
            ->with('success', 'Пользователь успешно удален!');
    }
    
    /**
     * Отображение списка опросов
     */
    public function surveys(Request $request)
    {
        $query = Survey::with('user');
        
        // Фильтрация и поиск
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }
        
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Сортировка
        $sortField = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortField, $sortOrder);
        
        $surveys = $query->paginate(15);
        
        return view('admin.surveys.index', compact('surveys'));
    }
    
    /**
     * Отображение деталей опроса
     */
    public function showSurvey(Survey $survey)
    {
        $survey->load(['questions', 'user']);
        
        // Получаем статистику ответов
        $responseCount = DB::table('answers')
            ->where('survey_id', $survey->id)
            ->select('session_id', 'user_id')
            ->distinct()
            ->count();
        
        return view('admin.surveys.show', compact('survey', 'responseCount'));
    }
    
    /**
     * Удаление опроса
     */
    public function destroySurvey(Survey $survey)
    {
        // Проверяем, чтобы обычный администратор не мог удалить опросы суперадмина
        $survey->load('user');
        if ($survey->user && $survey->user->is_super_admin && !auth()->user()->is_super_admin) {
            return redirect()->route('admin.surveys')
                ->with('error', 'Вы не можете удалить опрос, созданный суперадминистратором!');
        }
        
        // Сохраняем данные опроса для логирования
        $surveyData = $survey->toArray();
        $surveyTitle = $survey->title;
        $surveyId = $survey->id;
        
        $survey->delete();
        
        // Логирование удаления опроса
        ActivityLogService::logDeleted(
            'Survey',
            $surveyId,
            $surveyData,
            'Удаление опроса администратором: ' . $surveyTitle
        );
        
        return redirect()->route('admin.surveys')
            ->with('success', 'Опрос успешно удален!');
    }
    
    /**
     * Изменение статуса опроса (активный/неактивный)
     */
    public function toggleSurveyStatus(Survey $survey)
    {
        // Проверяем, чтобы обычный администратор не мог изменять статус опросов суперадмина
        $survey->load('user');
        if ($survey->user && $survey->user->is_super_admin && !auth()->user()->is_super_admin) {
            return redirect()->route('admin.surveys')
                ->with('error', 'Вы не можете изменять статус опроса, созданного суперадминистратором!');
        }
        
        // Сохраняем старые значения для логирования
        $oldValues = $survey->toArray();
        
        $survey->is_active = !$survey->is_active;
        $survey->save();
        
        $status = $survey->is_active ? 'активирован' : 'деактивирован';
        
        // Логирование изменения статуса опроса
        ActivityLogService::logUpdated(
            'Survey',
            $survey->id,
            $oldValues,
            $survey->toArray(),
            "Изменение статуса опроса: {$survey->title} (опрос {$status})"
        );
        
        return redirect()->route('admin.surveys')
            ->with('success', "Опрос успешно {$status}!");
    }
}
