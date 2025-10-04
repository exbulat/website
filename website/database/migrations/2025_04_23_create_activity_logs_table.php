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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // Действие (create, update, delete, login, etc.)
            $table->string('entity_type')->nullable(); // Тип сущности (User, Survey, etc.)
            $table->unsignedBigInteger('entity_id')->nullable(); // ID сущности
            $table->text('description')->nullable(); // Описание действия
            $table->json('old_values')->nullable(); // Старые значения (для update)
            $table->json('new_values')->nullable(); // Новые значения (для update)
            $table->string('ip_address')->nullable(); // IP-адрес
            $table->string('user_agent')->nullable(); // User-Agent
            $table->timestamps();
            
            // Индексы для ускорения поиска
            $table->index(['entity_type', 'entity_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
