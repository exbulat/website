<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'is_admin',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }
    
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
    
    /**
     * Получить опросы, созданные пользователем
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }
    
    /**
     * Получить архивированные опросы пользователя
     */
    public function archivedSurveys(): HasMany
    {
        return $this->hasMany(ArchivedSurvey::class);
    }
    
    /**
     * Получить ответы пользователя
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
    
    /**
     * Получить уведомления пользователя
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
    
    /**
     * Получить количество непрочитанных уведомлений
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }
    
    /**
     * Проверка, является ли пользователь администратором
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
    
    /**
     * Проверка, является ли пользователь суперадминистратором
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }
    
    /**
     * Получить логи активности пользователя
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
