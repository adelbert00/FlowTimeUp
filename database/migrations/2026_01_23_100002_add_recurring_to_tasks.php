<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('completed');
            }
            if (!Schema::hasColumn('tasks', 'recurrence_type')) {
                $table->enum('recurrence_type', ['daily', 'weekly', 'monthly'])->nullable()->after('is_recurring');
            }
            if (!Schema::hasColumn('tasks', 'recurrence_interval')) {
                $table->unsignedInteger('recurrence_interval')->default(1)->after('recurrence_type');
            }
            if (!Schema::hasColumn('tasks', 'recurrence_ends_at')) {
                $table->date('recurrence_ends_at')->nullable()->after('recurrence_interval');
            }
            if (!Schema::hasColumn('tasks', 'parent_task_id')) {
                $table->foreignId('parent_task_id')->nullable()->after('recurrence_ends_at')->constrained('tasks')->onDelete('set null');
            }
            if (!Schema::hasColumn('tasks', 'next_occurrence')) {
                $table->date('next_occurrence')->nullable()->after('parent_task_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_task_id']);
            $table->dropColumn(['is_recurring', 'recurrence_type', 'recurrence_interval', 'recurrence_ends_at', 'parent_task_id', 'next_occurrence']);
        });
    }
};
