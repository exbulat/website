<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSurveyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;

// Главная страница
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Маршруты аутентификации
Auth::routes();

// Перенаправление с /home на главную
Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

// Публичные маршруты для просмотра и поиска опросов
Route::get('surveys/public', [PublicSurveyController::class, 'index'])->name('public.surveys.index');
Route::get('surveys/public/popular', [PublicSurveyController::class, 'popular'])->name('public.surveys.popular');
Route::get('surveys/public/completed', [PublicSurveyController::class, 'completed'])->name('public.surveys.completed');
Route::get('surveys/public/search', [PublicSurveyController::class, 'search'])->name('public.surveys.search');

// Маршруты для опросов (требуют аутентификации)
Route::middleware('auth')->group(function () {
    // История опросов - должен быть перед ресурсным маршрутом
    Route::get('surveys/history', [SurveyController::class, 'history'])->name('surveys.history');
    
    // Архив опросов
    Route::get('surveys/archive', [SurveyController::class, 'archive'])->name('surveys.archive.index');
    Route::get('surveys/archive/{id}', [SurveyController::class, 'showArchived'])->name('surveys.archive.show');
    Route::delete('surveys/archive/{id}', [SurveyController::class, 'destroyArchived'])->name('surveys.archive.destroy');
    Route::post('surveys/{survey}/archive', [SurveyController::class, 'archiveSurvey'])->name('surveys.archive.store');
    
    // Управление опросами
    Route::resource('surveys', SurveyController::class);
    
    // Экспорт результатов
    Route::get('surveys/{survey}/export/{format}', [SurveyController::class, 'export'])->name('surveys.export');
    
    // Результаты опросов
    Route::get('surveys/{survey}/results', [SurveyController::class, 'results'])->name('surveys.results');
    
    // QR-код для опроса
    Route::get('surveys/{survey}/qrcode', [SurveyController::class, 'qrcode'])->name('surveys.qrcode');
    
    // Управление вопросами
    Route::get('surveys/{survey}/questions/create', [QuestionController::class, 'create'])->name('surveys.questions.create');
    Route::post('surveys/{survey}/questions', [QuestionController::class, 'store'])->name('surveys.questions.store');
    Route::get('surveys/{survey}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('surveys.questions.edit');
    Route::put('surveys/{survey}/questions/{question}', [QuestionController::class, 'update'])->name('surveys.questions.update');
    Route::delete('surveys/{survey}/questions/{question}', [QuestionController::class, 'destroy'])->name('surveys.questions.destroy');
    Route::post('surveys/{survey}/questions/reorder', [QuestionController::class, 'reorder'])->name('surveys.questions.reorder');
    
    // Профиль пользователя
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
    Route::get('profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password.form');
    Route::post('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    
    // Уведомления
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Маршруты администратора
    Route::prefix('admin')->middleware(\App\Http\Middleware\AdminMiddleware::class)->name('admin.')->group(function () {
        // Дашборд
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        
        // Управление пользователями
        Route::get('users', [AdminController::class, 'users'])->name('users');
        Route::get('users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        
        // Управление опросами
        Route::get('surveys', [AdminController::class, 'surveys'])->name('surveys');
        Route::get('surveys/{survey}', [AdminController::class, 'showSurvey'])->name('surveys.show');
        Route::delete('surveys/{survey}', [AdminController::class, 'destroySurvey'])->name('surveys.destroy');
        Route::post('surveys/{survey}/toggle', [AdminController::class, 'toggleSurveyStatus'])->name('surveys.toggle');
        
        // Маршруты для логов активности (только для суперадминов)
        Route::middleware(\App\Http\Middleware\SuperAdminMiddleware::class)->group(function () {
            Route::get('logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('logs.index');
            Route::get('logs/export', [\App\Http\Controllers\ActivityLogController::class, 'export'])->name('logs.export');
            Route::get('logs/{log}', [\App\Http\Controllers\ActivityLogController::class, 'show'])->name('logs.show');
            Route::delete('logs/{log}', [\App\Http\Controllers\ActivityLogController::class, 'destroy'])->name('logs.destroy');
            Route::delete('logs', [\App\Http\Controllers\ActivityLogController::class, 'clearAll'])->name('logs.clear');
        });
    });
});

// Публичные маршруты для прохождения опросов
Route::get('s/{code}/take', [SurveyController::class, 'take'])->name('surveys.take');
Route::post('s/{code}/submit', [AnswerController::class, 'store'])->name('surveys.submit');
Route::get('s/{code}/thank-you', [AnswerController::class, 'thankYou'])->name('surveys.thank-you');
Route::get('s/{code}/responses', [SurveyController::class, 'viewResponses'])->name('surveys.view-responses');
