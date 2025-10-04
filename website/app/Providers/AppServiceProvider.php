<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use App\Services\ActivityLogService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Оптимизация: отключаем запросы SQL в режиме отладки для продакшена
        if (!config('app.debug')) {
            DB::disableQueryLog();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Оптимизация: устанавливаем длину строки по умолчанию для MySQL
        Schema::defaultStringLength(191);
        
        // Оптимизация: отключаем ленивую загрузку в продакшене для предотвращения N+1 запросов
        Model::preventLazyLoading(!app()->isProduction());
        
        // Оптимизация: включаем кэширование для часто используемых запросов
        if (app()->isProduction()) {
            // Глобальное кэширование для повышения производительности
            $this->setupGlobalCaching();
        }
        
        // Оптимизация: настройка HTTPS для продакшена
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }
        
        // Оптимизация: логирование медленных запросов
        $this->logSlowQueries();
        
        // Настройка логирования действий пользователей
        $this->setupActivityLogging();
    }
    
    /**
     * Настройка глобального кэширования для повышения производительности
     */
    private function setupGlobalCaching(): void
    {
        // Слушаем событие запроса для кэширования ответов на GET-запросы
        Event::listen('Illuminate\Foundation\Http\Events\RequestHandled', function ($event) {
            $request = $event->request;
            $response = $event->response;
            
            // Кэшируем только GET-запросы без ошибок
            if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
                $key = 'response_cache_' . md5($request->fullUrl());
                
                // Кэшируем на 5 минут для часто меняющихся данных
                if (!Cache::has($key)) {
                    Cache::put($key, $response->getContent(), now()->addMinutes(5));
                }
            }
        });
    }
    
    /**
     * Логирование медленных запросов для оптимизации
     */
    private function logSlowQueries(): void
    {
        DB::listen(function ($query) {
            // Логируем запросы, которые выполняются дольше 1 секунды
            if ($query->time > 1000) {
                Log::channel('slow-queries')->warning(
                    'Slow query detected: ' . $query->sql,
                    [
                        'time' => $query->time,
                        'bindings' => $query->bindings
                    ]
                );
            }
        });
    }
    
    /**
     * Настройка логирования действий пользователей
     */
    private function setupActivityLogging(): void
    {
        // Логирование входа пользователя в систему
        Event::listen(Login::class, function (Login $event) {
            ActivityLogService::logLogin($event->user->id);
        });
        
        // Логирование выхода пользователя из системы
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                ActivityLogService::logLogout($event->user->id);
            }
        });
        
        // Логирование регистрации пользователя
        Event::listen(Registered::class, function (Registered $event) {
            ActivityLogService::logCreated(
                'User',
                $event->user->id,
                $event->user->toArray(),
                'Регистрация нового пользователя'
            );
        });
        
        // Логирование сброса пароля
        Event::listen(PasswordReset::class, function (PasswordReset $event) {
            ActivityLogService::log(
                'password_reset',
                'User',
                $event->user->id,
                'Сброс пароля пользователя'
            );
        });
    }
}
