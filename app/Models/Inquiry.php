<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'room_id',
        'landlord_id',
        'renter_id',
        'age',
        'gender',
        'status',
        'rent_started_at'
    ];

    /**
     * Attribute casting configuration.
     */
    protected $casts = [
        'rent_started_at' => 'datetime', // Turns DB timestamp into a workable Carbon date object
    ];

    /**
     * Guardrail hook bypass for legacy schema configurations
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            if (\Schema::hasColumn('inquiries', 'user_id')) {
                $inquiry->user_id = $inquiry->renter_id ?? 0;
            }
        });
    }

    /**
     * Relationship: An inquiry references a single room asset.
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /**
     * Relationship: An inquiry connects to a managing landlord account.
     */
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Relationship: An inquiry belongs to the applicant student renter.
     */
    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }
}