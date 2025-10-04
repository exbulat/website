<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    // Типы вопросов
    public const TYPE_SINGLE_CHOICE = 'single_choice';
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_TEXT = 'text';
    public const TYPE_SCALE = 'scale';

    protected $fillable = [
        'survey_id',
        'title',
        'description',
        'type',
        'options',
        'position',
        'is_required',
        'time_limit',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    /**
     * Получить опрос, к которому относится вопрос
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Получить ответы на вопрос
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Получить все возможные типы вопросов
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_SINGLE_CHOICE => 'Одиночный выбор',
            self::TYPE_MULTIPLE_CHOICE => 'Множественный выбор',
            self::TYPE_TEXT => 'Текстовый ответ',
            self::TYPE_SCALE => 'Шкала',
        ];
    }
}
