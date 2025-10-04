<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * Имя таблицы, связанной с моделью.
     *
     * @var string
     */
    protected $table = 'survey_notifications';
    
    protected $fillable = [
        'user_id',
        'survey_id',
        'type',
        'message',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    /**
     * Получить пользователя, которому принадлежит уведомление
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить опрос, к которому относится уведомление
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Отметить уведомление как прочитанное
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();

        return $this;
    }

    /**
     * Отметить уведомление как непрочитанное
     */
    public function markAsUnread()
    {
        $this->is_read = false;
        $this->read_at = null;
        $this->save();

        return $this;
    }
}
