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
        Schema::table('inquiries', function (Blueprint $table) {
            // Check first to ensure no collisions occur before altering fields
            if (!Schema::hasColumn('inquiries', 'rent_started_at')) {
                $table->timestamp('rent_started_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('inquiries', 'rent_started_at')) {
                $table->dropColumn('rent_started_at');
            }
        });
    }
};