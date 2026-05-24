<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $fillable = [
        'room_id', 'renter_name', 'renter_contact', 'renter_photo', 'is_converted',
    ];

    protected $casts = [
        'is_converted' => 'boolean',
    ];

    public function room(): BelongsTo { return $this->belongsTo(Room::class); }
}