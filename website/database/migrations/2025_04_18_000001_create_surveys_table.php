<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->string('code')->unique(); // Код для доступа к опросу
            $table->json('design')->nullable(); // Настройки дизайна (цвета, шрифты и т.д.)
            $table->boolean('is_public')->default(true); // Публичный или приватный опрос
            $table->timestamp('start_at')->nullable(); // Время начала опроса
            $table->timestamp('end_at')->nullable(); // Время окончания опроса
            $table->integer('time_limit')->nullable(); // Ограничение времени в секундах
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
