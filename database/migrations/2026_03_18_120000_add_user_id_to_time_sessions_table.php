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
        // Poprawka dla PostgreSQL/SQLite: Używamy podzapytania zamiast join w update
        DB::table('time_sessions')->update([
            'user_id' => DB::table('tasks')
                ->whereColumn('tasks.id', 'time_sessions.task_id')
                ->select('user_id')
                ->limit(1)
        ]);

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
