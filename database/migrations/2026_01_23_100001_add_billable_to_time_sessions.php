<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('time_sessions', 'is_billable')) {
                $table->boolean('is_billable')->default(true)->after('end_time');
            }
            if (!Schema::hasColumn('time_sessions', 'billable_rate')) {
                $table->decimal('billable_rate', 10, 2)->nullable()->after('is_billable');
            }
            if (!Schema::hasColumn('time_sessions', 'description')) {
                $table->text('description')->nullable()->after('billable_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('time_sessions', function (Blueprint $table) {
            $table->dropColumn(['is_billable', 'billable_rate', 'description']);
        });
    }
};
