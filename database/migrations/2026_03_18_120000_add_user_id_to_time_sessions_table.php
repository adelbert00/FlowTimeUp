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
        DB::table('time_sessions')
            ->join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->update(['time_sessions.user_id' => DB::raw('tasks.user_id')]);

        Schema::table('time_sessions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
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
