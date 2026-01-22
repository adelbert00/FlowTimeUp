<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable()->after('title');
            $table->date('due_date')->nullable()->after('description');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('due_date');
            $table->boolean('completed')->default(false)->after('priority');
            
            $table->index('user_id');
            $table->index('project_id');
            $table->index('due_date');
            $table->index('completed');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['project_id']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['completed']);
            
            $table->dropColumn(['user_id', 'description', 'due_date', 'priority', 'completed']);
        });
    }
};
