<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'room_number',
        'monthly_rate',
        'nearest_school',
        'distance_indicator',
        'amenities',
        'room_photo_1',
        'room_photo_2',
        'room_photo_3',
        'is_available',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'amenities' => 'array',
        'is_available' => 'boolean',
    ];

    /**
     * NEW RELATIONSHIP FIX: A room belongs to a User (the Landlord owner).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: A room can have many inbound student inquiries.
     */
    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'room_id');
    }
}