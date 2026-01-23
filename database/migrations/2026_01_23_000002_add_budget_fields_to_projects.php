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
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'hourly_rate')) {
                $table->decimal('hourly_rate', 10, 2)->nullable()->after('color');
            }
            if (!Schema::hasColumn('projects', 'budget')) {
                $table->decimal('budget', 10, 2)->nullable()->after('hourly_rate');
            }
            if (!Schema::hasColumn('projects', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('budget');
            }
            if (!Schema::hasColumn('projects', 'access')) {
                $table->string('access', 10)->default('public')->after('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'hourly_rate')) {
                $table->dropColumn('hourly_rate');
            }
            if (Schema::hasColumn('projects', 'budget')) {
                $table->dropColumn('budget');
            }
            if (Schema::hasColumn('projects', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('projects', 'access')) {
                $table->dropColumn('access');
            }
        });
    }
};
