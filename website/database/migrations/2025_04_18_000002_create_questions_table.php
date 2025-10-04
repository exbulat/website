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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type'); // single_choice, multiple_choice, text, scale
            $table->json('options')->nullable(); // Варианты ответов для выбора
            $table->integer('position')->default(0); // Позиция вопроса в опросе
            $table->boolean('is_required')->default(false);
            $table->integer('time_limit')->nullable(); // Ограничение времени на вопрос в секундах
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
