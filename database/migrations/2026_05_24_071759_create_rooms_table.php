<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Landlord Owner Link
            $table->string('room_number');
            $table->decimal('monthly_rate', 8, 2);
            $table->string('nearest_school'); // School Location Filter Value
            $table->string('distance_indicator'); // e.g., "5 mins walk", "500m away"
            $table->json('amenities'); // Array of items
            $table->string('room_photo_1');
            $table->string('room_photo_2')->nullable();
            $table->string('room_photo_3')->nullable();
            $table->boolean('is_available')->default(true); // True = Green, False = Red
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rooms');
    }
};