<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;


class Survey extends Model
{
    use HasFactory;

    // Включаем ленивую загрузку
    protected $with = ['questions', 'answers'];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'slug',
        'code',
        'access_code',
        'design',
        'is_public',
        'start_at',
        'end_at',
        'time_limit',
        'is_active',
        'show_results',
        'is_archived',
        'archived_at'
    ];

    protected $casts = [
        'design' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'show_results' => 'boolean',
        'is_archived' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Генерация уникального кода и слага при создании опроса
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($survey) {
            $survey->slug = $survey->slug ?? Str::slug($survey->title);
            $survey->code = $survey->code ?? Str::random(8);
        });
    }

    /**
     * Получить пользователя, создавшего опрос
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить вопросы опроса
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    /**
     * Получить ответы на опрос
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
    
    /**
     * Получить уведомления опроса
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Проверить, активен ли опрос
     */
    public function isActive(): bool
    {
        // Архивированные опросы считаются неактивными
        if ($this->is_archived) {
            return false;
        }

        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_at && $now->lt($this->start_at)) {
            return false;
        }

        if ($this->end_at && $now->gt($this->end_at)) {
            return false;
        }

        return true;
    }
}
