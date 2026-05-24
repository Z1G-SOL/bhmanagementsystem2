<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('room_number');
            $table->decimal('monthly_rate', 10, 2);
            $table->string('nearest_school');
            $table->string('distance_indicator');
            $table->json('amenities')->nullable();
            $table->string('room_photo_1')->nullable();
            $table->string('room_photo_2')->nullable();
            $table->string('room_photo_3')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};