<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Survey;
use App\Models\Answer;

class ProfileController extends Controller
{
    /**
     * Показать профиль пользователя
     */
    public function show()
    {
        $user = Auth::user();
        
        // Статистика пользователя
        $stats = [
            'surveys_created' => $user->surveys()->count(),
            'surveys_participated' => $user->answers()->distinct('survey_id')->count(),
            'total_answers' => $user->answers()->count(),
        ];
        
        return view('profile.show', compact('user', 'stats'));
    }
    
    /**
     * Показать форму редактирования профиля
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    
    /**
     * Обновить данные профиля
     */
    public function update(Request $request)
{
    $user = Auth::user();
    
    // Валидация данных
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    
    // Обновляем основные данные
    $user->name = $validated['name'];
    $user->email = $validated['email'];
    
    // Обрабатываем загрузку аватара
    if ($request->hasFile('avatar')) {
        // Удаляем старый аватар, если он есть
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Сохраняем новый аватар
        $image = $request->file('avatar');
        $filename = $image->getClientOriginalName();  // Сохраняем оригинальное имя файла
        $image->move(public_path('storage/avatars'), $filename); // Это сохранит изображение в public/storage/avatars
        $user->avatar = 'avatars/' . $filename; // Это путь, который сохраняется в базе данных

    }
    
    // Сохраняем изменения
    $user->save();
    
    return redirect()->route('profile.show')
        ->with('success', 'Профиль успешно обновлен');
}


    /**
     * Показать форму смены пароля
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }
    
    /**
     * Обновить пароль пользователя
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        // Проверяем текущий пароль
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Текущий пароль указан неверно']);
        }
        
        // Обновляем пароль
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        return redirect()->route('profile.show')
            ->with('success', 'Пароль успешно изменен');
    }
}
