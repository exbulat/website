<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicSurveyController extends Controller
{
    /**
     * Отображение списка публичных опросов
     */
    public function index()
    {
        // Получаем активные публичные опросы
        $surveys = Survey::where('is_public', true)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            })
            ->latest()
            ->paginate(12);
            
        // Получаем статистику для каждого опроса
        foreach ($surveys as $survey) {
            $anonymousCount = DB::table('answers')
                ->where('survey_id', $survey->id)
                ->whereNotNull('session_id')
                ->select('session_id')
                ->distinct()
                ->count();
                
            $userCount = DB::table('answers')
                ->where('survey_id', $survey->id)
                ->whereNotNull('user_id')
                ->select('user_id')
                ->distinct()
                ->count();
                
            $survey->responses_count = $anonymousCount + $userCount;
            $survey->questions_count = $survey->questions()->count();
        }
        
        return view('surveys.public-index', compact('surveys'));
    }
    
    /**
     * Отображение популярных опросов
     */
    public function popular()
    {
        // Получаем активные публичные опросы
        $surveys = Survey::where('is_public', true)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            })
            ->latest()
            ->paginate(12);
            
        // Получаем статистику для каждого опроса
        foreach ($surveys as $survey) {
            $anonymousCount = DB::table('answers')
                ->where('survey_id', $survey->id)
                ->whereNotNull('session_id')
                ->select('session_id')
                ->distinct()
                ->count();
                
            $userCount = DB::table('answers')
                ->where('survey_id', $survey->id)
                ->whereNotNull('user_id')
                ->select('user_id')
                ->distinct()
                ->count();
                
            $survey->responses_count = $anonymousCount + $userCount;
            $survey->questions_count = $survey->questions()->count();
        }
        
        // Сортируем по количеству ответов
        $surveys = $surveys->sortByDesc('responses_count');
        // Преобразуем обратно в коллекцию для пагинации
        $surveys = new \Illuminate\Pagination\LengthAwarePaginator(
            $surveys->values(),
            $surveys->count(),
            12,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        return view('surveys.public-popular', compact('surveys'));
    }
    
    /**
     * Отображение недавно завершенных опросов
     */
    public function completed()
    {
        // Получаем недавно завершенные публичные опросы
        $surveys = Survey::where('is_public', true)
            ->where('is_active', true)
            ->where('end_at', '<', now())
            // Временно убираем проверку show_results, пока миграция не будет применена
            // ->where('show_results', true)
            ->latest('end_at')
            ->paginate(12);
            
        // Получаем статистику для каждого опроса
        foreach ($surveys as $survey) {
            $count = DB::table('answers')
                ->where('survey_id', $survey->id)
                ->select(DB::raw('count(distinct COALESCE(user_id, session_id)) as count'))
                ->first();
                
            $survey->responses_count = $count ? $count->count : 0;
            $survey->questions_count = $survey->questions()->count();
        }
        
        return view('surveys.public-completed', compact('surveys'));
    }
    
    /**
     * Поиск опросов
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('public.surveys.index');
        }
        
        // Ищем опросы по заголовку или описанию
        $surveys = Survey::where('is_public', true)
            ->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->where(function($q) {
                $q->whereNull('end_at')
                  ->orWhere('end_at', '>=', now());
            })
            ->latest()
            ->paginate(12);
            
        // Получаем статистику для каждого опроса
        foreach ($surveys as $survey) {
            $count = DB::table('answers')
                ->where('survey_id', $survey->id)
                ->select(DB::raw('count(distinct COALESCE(user_id, session_id)) as count'))
                ->first();
                
            $survey->responses_count = $count ? $count->count : 0;
            $survey->questions_count = $survey->questions()->count();
        }
        
        return view('surveys.public-search', compact('surveys', 'query'));
    }
}
