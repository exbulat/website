<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Показать все уведомления пользователя
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->with('survey')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Отметить уведомление как прочитанное
     */
    public function markAsRead(Notification $notification)
    {
        // Проверяем, что уведомление принадлежит текущему пользователю
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Уведомление отмечено как прочитанное');
    }
    
    /**
     * Отметить все уведомления как прочитанные
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return redirect()->back()->with('success', 'Все уведомления отмечены как прочитанные');
    }
    
    /**
     * Удалить уведомление
     */
    public function destroy(Notification $notification)
    {
        // Проверяем, что уведомление принадлежит текущему пользователю
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        
        $notification->delete();
        
        return redirect()->route('notifications.index')
            ->with('success', 'Уведомление удалено');
    }
}
