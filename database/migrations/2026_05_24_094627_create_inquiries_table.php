<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('renter_id')->nullable()->constrained('users')->onDelete('set null'); // Nullable if applying publicly
            $table->string('name'); // Fallback direct name entry string
            $table->string('contact_number'); // Contact metadata
            $table->integer('age');
            $table->string('gender');
            $table->string('valid_id_path'); // Mandatory ID Image Upload Path
            $table->string('status')->default('Pending'); // Pending, Accepted, Rejected, Terminated
            $table->timestamp('rent_started_at')->nullable(); // For 30-day billing tracker countdown
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inquiries');
    }
};