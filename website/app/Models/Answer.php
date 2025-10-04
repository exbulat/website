<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'question_id',
        'user_id',
        'session_id',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Получить опрос, к которому относится ответ
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Получить вопрос, на который дан ответ
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Получить пользователя, давшего ответ (если не анонимный)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
