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
            // Step 1: Drop the foreign key constraint binding the index
            if (Schema::hasColumn('inquiries', 'user_id')) {
                $table->dropForeign(['user_id']); // Automatically resolves to inquiries_user_id_foreign
                
                // Step 2: Now that it's unlinked, safely drop the column
                $table->dropColumn('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('inquiries', 'user_id')) {
                // Reconstruct the field column and relationship if rolled back
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
        });
    }
};