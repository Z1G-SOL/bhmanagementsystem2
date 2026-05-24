<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            // Safe-check to avoid duplicate column errors
            if (!Schema::hasColumn('inquiries', 'renter_id')) {
                $table->unsignedBigInteger('renter_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('inquiries', 'landlord_id')) {
                $table->unsignedBigInteger('landlord_id')->nullable()->after('renter_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['renter_id', 'landlord_id']);
        });
    }
};