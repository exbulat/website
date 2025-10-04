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
        Schema::create('archived_surveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_id')->comment('Идентификатор оригинального опроса');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->string('code', 10)->unique();
            $table->string('access_code')->nullable();
            $table->json('design')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_results')->default(true);
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->integer('time_limit')->nullable();
            $table->integer('views')->default(0);
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_surveys');
    }
};
