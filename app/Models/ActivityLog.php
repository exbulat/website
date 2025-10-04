<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    /**
     * Атрибуты, которые должны быть приведены к нативным типам.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    /**
     * Получить пользователя, выполнившего действие.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить имя пользователя, выполнившего действие.
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Система';
    }

    /**
     * Получить название действия на русском языке.
     */
    public function getActionNameAttribute(): string
    {
        $actions = [
            'create' => 'Создание',
            'update' => 'Обновление',
            'delete' => 'Удаление',
            'login' => 'Вход в систему',
            'logout' => 'Выход из системы',
            'register' => 'Регистрация',
            'password_reset' => 'Сброс пароля',
            'password_change' => 'Изменение пароля',
            'role_change' => 'Изменение роли',
            'view' => 'Просмотр',
            'export' => 'Экспорт данных',
            'import' => 'Импорт данных',
        ];

        return $actions[$this->action] ?? $this->action;
    }

    /**
     * Получить название сущности на русском языке.
     */
    public function getEntityNameAttribute(): string
    {
        $entities = [
            'User' => 'Пользователь',
            'Survey' => 'Опрос',
            'Question' => 'Вопрос',
            'Answer' => 'Ответ',
            'ArchivedSurvey' => 'Архивированный опрос',
            'Notification' => 'Уведомление',
        ];

        return $entities[$this->entity_type] ?? $this->entity_type;
    }
}
