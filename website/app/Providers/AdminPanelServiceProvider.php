<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPanelServiceProvider extends ServiceProvider
{
    /**
     * Регистрация сервисов
     */
    public function register(): void
    {
        //
    }

    /**
     * Загрузка сервисов
     */
    public function boot(): void
    {
        // Проверка и создание администратора, если в системе нет пользователей с правами админа
        $this->ensureAdminExists();
    }

    /**
     * Проверка наличия администратора в системе
     */
    protected function ensureAdminExists(): void
    {
        try {
            // Проверяем наличие администраторов
            $hasAdmin = User::where('is_admin', true)->exists();
            
            if (!$hasAdmin) {
                // Создаем начального администратора
                User::create([
                    'name' => 'Administrator',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('admin123'),
                    'is_admin' => true,
                ]);
                
                if ($this->app->environment('local')) {
                    echo "Создан администратор по умолчанию: admin@example.com / admin123\n";
                }
            }
        } catch (\Exception $e) {
            // В случае ошибки (например, таблица еще не создана) просто игнорируем
            if ($this->app->environment('local')) {
                echo "Ошибка при создании администратора: " . $e->getMessage() . "\n";
            }
        }
    }
}
