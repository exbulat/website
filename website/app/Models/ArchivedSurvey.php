<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchivedSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_id', 'user_id', 'title', 'description', 'slug', 'code',
        'access_code', 'design', 'is_public', 'is_active', 'show_results',
        'start_at', 'end_at', 'time_limit', 'views', 'archived_at'
    ];

    protected $casts = [
        'design' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'show_results' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Получить пользователя, создавшего опрос
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить вопросы архивированного опроса
     */
    public function questions()
    {
        return Question::where('survey_id', $this->original_id)->orderBy('position')->get();
    }

    /**
     * Получить ответы на архивированный опрос
     */
    public function answers()
    {
        return Answer::where('survey_id', $this->original_id)->get();
    }
}
