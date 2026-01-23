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
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'hourly_rate')) {
                $table->decimal('hourly_rate', 10, 2)->nullable()->after('priority');
            }
            if (!Schema::hasColumn('tasks', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('hourly_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'hourly_rate')) {
                $table->dropColumn('hourly_rate');
            }
            if (Schema::hasColumn('tasks', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
