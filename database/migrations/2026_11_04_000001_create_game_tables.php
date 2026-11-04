<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->string('name', 9)->nullable()->index();
            $table->timestamps();
        });

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('difficulty');
            $table->text('prompt');
            $table->string('image_path')->nullable();
            $table->string('praise')->nullable();
            $table->string('hint')->nullable();
            $table->json('payload');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['game_id', 'difficulty', 'is_active']);
        });

        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('difficulty');
            $table->string('status')->default('active');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedSmallInteger('total_questions')->default(0);
            $table->unsignedSmallInteger('correct_count')->default(0);
            $table->unsignedSmallInteger('incorrect_count')->default(0);
            $table->unsignedSmallInteger('clues_used')->default(0);
            $table->unsignedInteger('score')->default(0);
            $table->json('question_ids');
            $table->timestamps();

            $table->index(['player_id', 'status']);
        });

        Schema::create('game_session_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->json('presentation');
            $table->json('progress')->nullable();
            $table->unsignedSmallInteger('mistakes')->default(0);
            $table->unsignedSmallInteger('clues_used')->default(0);
            $table->boolean('is_correct')->nullable();
            $table->unsignedSmallInteger('score')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['game_session_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_session_questions');
        Schema::dropIfExists('game_sessions');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('games');
        Schema::dropIfExists('players');
    }
};
