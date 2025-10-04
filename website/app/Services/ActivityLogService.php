<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Логирование действия пользователя
     *
     * @param string $action Действие (create, update, delete, login, etc.)
     * @param string|null $entityType Тип сущности (User, Survey, etc.)
     * @param int|null $entityId ID сущности
     * @param string|null $description Описание действия
     * @param array|null $oldValues Старые значения (для update)
     * @param array|null $newValues Новые значения (для update)
     * @return ActivityLog
     */
    public static function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Логирование создания сущности
     *
     * @param string $entityType Тип сущности
     * @param int $entityId ID сущности
     * @param array $values Значения
     * @param string|null $description Описание
     * @return ActivityLog
     */
    public static function logCreated(
        string $entityType,
        int $entityId,
        array $values,
        ?string $description = null
    ): ActivityLog {
        return self::log(
            'create',
            $entityType,
            $entityId,
            $description ?? "Создание {$entityType} #{$entityId}",
            null,
            $values
        );
    }

    /**
     * Логирование обновления сущности
     *
     * @param string $entityType Тип сущности
     * @param int $entityId ID сущности
     * @param array $oldValues Старые значения
     * @param array $newValues Новые значения
     * @param string|null $description Описание
     * @return ActivityLog
     */
    public static function logUpdated(
        string $entityType,
        int $entityId,
        array $oldValues,
        array $newValues,
        ?string $description = null
    ): ActivityLog {
        return self::log(
            'update',
            $entityType,
            $entityId,
            $description ?? "Обновление {$entityType} #{$entityId}",
            $oldValues,
            $newValues
        );
    }

    /**
     * Логирование удаления сущности
     *
     * @param string $entityType Тип сущности
     * @param int $entityId ID сущности
     * @param array $values Значения
     * @param string|null $description Описание
     * @return ActivityLog
     */
    public static function logDeleted(
        string $entityType,
        int $entityId,
        array $values,
        ?string $description = null
    ): ActivityLog {
        return self::log(
            'delete',
            $entityType,
            $entityId,
            $description ?? "Удаление {$entityType} #{$entityId}",
            $values,
            null
        );
    }

    /**
     * Логирование входа пользователя в систему
     *
     * @param int $userId ID пользователя
     * @return ActivityLog
     */
    public static function logLogin(int $userId): ActivityLog
    {
        return self::log(
            'login',
            'User',
            $userId,
            "Вход пользователя в систему"
        );
    }

    /**
     * Логирование выхода пользователя из системы
     *
     * @param int $userId ID пользователя
     * @return ActivityLog
     */
    public static function logLogout(int $userId): ActivityLog
    {
        return self::log(
            'logout',
            'User',
            $userId,
            "Выход пользователя из системы"
        );
    }

    /**
     * Логирование изменения роли пользователя
     *
     * @param int $userId ID пользователя
     * @param array $oldValues Старые значения
     * @param array $newValues Новые значения
     * @return ActivityLog
     */
    public static function logRoleChange(
        int $userId,
        array $oldValues,
        array $newValues
    ): ActivityLog {
        return self::log(
            'role_change',
            'User',
            $userId,
            "Изменение роли пользователя",
            $oldValues,
            $newValues
        );
    }
}
