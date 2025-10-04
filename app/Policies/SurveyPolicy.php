<?php

namespace App\Policies;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SurveyPolicy
{
    use HandlesAuthorization;

    /**
     * Определяет, может ли пользователь просматривать список всех опросов.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Определяет, может ли пользователь просматривать опрос.
     */
    public function view(User $user, Survey $survey): bool
    {
        // Владелец опроса может просматривать свой опрос и его результаты
        if ($user->id === $survey->user_id) {
            return true;
        }

        // Для остальных пользователей - только если опрос публичный и это не просмотр результатов
        $isViewingResults = request()->route()->getName() === 'surveys.results';
        return $survey->is_public && !$isViewingResults;
    }

    /**
     * Определяет, может ли пользователь создавать опросы.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Определяет, может ли пользователь обновлять опрос.
     */
    public function update(User $user, Survey $survey): bool
    {
        return $user->id === $survey->user_id;
    }

    /**
     * Определяет, может ли пользователь удалять опрос.
     */
    public function delete(User $user, Survey $survey): bool
    {
        return $user->id === $survey->user_id;
    }
}
