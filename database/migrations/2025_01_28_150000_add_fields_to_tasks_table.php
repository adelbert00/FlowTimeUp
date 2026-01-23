<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('tasks', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('tasks', 'due_date')) {
                $table->date('due_date')->nullable()->after('description');
            }
            if (!Schema::hasColumn('tasks', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('due_date');
            }
            if (!Schema::hasColumn('tasks', 'completed')) {
                $table->boolean('completed')->default(false)->after('priority');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('tasks', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('tasks', 'due_date')) {
                $table->dropColumn('due_date');
            }
            if (Schema::hasColumn('tasks', 'priority')) {
                $table->dropColumn('priority');
            }
            if (Schema::hasColumn('tasks', 'completed')) {
                $table->dropColumn('completed');
            }
        });
    }
};
