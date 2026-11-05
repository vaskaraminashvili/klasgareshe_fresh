<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('subject', 50)->nullable()->after('game_id');
            $table->unsignedTinyInteger('grade')->nullable()->after('subject');
            // Book questions praise with a quote from the lesson, which can pass 255 characters.
            $table->text('praise')->nullable()->change();
            $table->text('hint')->nullable()->change();

            $table->index(['subject', 'grade']);
        });

        // A session remembers its filter so "play again" keeps the same subject and grade.
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->string('subject', 50)->nullable()->after('game_id');
            $table->unsignedTinyInteger('grade')->nullable()->after('subject');
        });
    }

    public function down(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropColumn(['subject', 'grade']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['subject', 'grade']);
            $table->dropColumn(['subject', 'grade']);
            $table->string('praise')->nullable()->change();
            $table->string('hint')->nullable()->change();
        });
    }
};
