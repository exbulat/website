<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    /**
     * Создание нового экземпляра контроллера
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\SuperAdminMiddleware::class);
    }

    /**
     * Отображение списка логов активности
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();
        
        // Фильтрация по пользователю
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Фильтрация по действию
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Фильтрация по типу сущности
        if ($request->has('entity_type') && $request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }
        
        // Фильтрация по дате
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Поиск по описанию
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        
        // Получение логов с пагинацией
        $logs = $query->paginate(20);
        
        // Получение списка пользователей для фильтра
        $users = User::orderBy('name')->get(['id', 'name']);
        
        // Получение списка действий для фильтра
        $actions = ActivityLog::select('action')
            ->distinct()
            ->pluck('action')
            ->toArray();
        
        // Получение списка типов сущностей для фильтра
        $entityTypes = ActivityLog::select('entity_type')
            ->whereNotNull('entity_type')
            ->distinct()
            ->pluck('entity_type')
            ->toArray();
        
        return view('admin.logs.index', compact(
            'logs',
            'users',
            'actions',
            'entityTypes'
        ));
    }

    /**
     * Отображение детальной информации о логе активности
     */
    public function show(ActivityLog $log)
    {
        return view('admin.logs.show', compact('log'));
    }

    /**
     * Удаление лога активности
     */
    public function destroy(ActivityLog $log)
    {
        $log->delete();
        
        return redirect()->route('admin.logs.index')
            ->with('success', 'Лог активности успешно удален');
    }

    /**
     * Очистка всех логов активности
     */
    public function clearAll()
    {
        DB::table('activity_logs')->truncate();
        
        return redirect()->route('admin.logs.index')
            ->with('success', 'Все логи активности успешно удалены');
    }

    /**
     * Экспорт логов активности в CSV
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user')->latest();
        
        // Применяем те же фильтры, что и для отображения
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        if ($request->has('entity_type') && $request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        
        $logs = $query->get();
        
        // Формируем CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="activity_logs_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Добавляем BOM для корректного отображения кириллицы в Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Заголовки
            fputcsv($file, [
                'ID',
                'Пользователь',
                'Действие',
                'Тип сущности',
                'ID сущности',
                'Описание',
                'IP-адрес',
                'Дата и время'
            ]);
            
            // Данные
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'Система',
                    $log->action_name,
                    $log->entity_name,
                    $log->entity_id,
                    $log->description,
                    $log->ip_address,
                    $log->created_at->format('d.m.Y H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
