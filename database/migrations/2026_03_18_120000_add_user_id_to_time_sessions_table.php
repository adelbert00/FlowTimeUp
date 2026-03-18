<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('time_sessions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('task_id')->constrained()->onDelete('cascade');
        });

        // Wypełnij user_id na podstawie relacji przez tabelę tasks
        // Używamy raw SQL z podzapytaniem, co jest najbardziej niezawodne w migracjach cross-DB
        DB::statement('UPDATE time_sessions SET user_id = (SELECT user_id FROM tasks WHERE tasks.id = time_sessions.task_id)');

        Schema::table('time_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
