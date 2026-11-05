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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->unique()->after('name');
            $table->unsignedInteger('rank')->default(0)->after('password');
            $table->string('gender', 10)->after('rank');
            $table->unsignedTinyInteger('grade')->nullable()->after('gender');
            $table->string('avatar')->nullable()->after('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'rank', 'gender', 'grade', 'avatar']);
        });
    }
};
