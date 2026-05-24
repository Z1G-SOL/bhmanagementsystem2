<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relationship: A renter can send many inquiries.
     */
    public function inquiriesSent()
    {
        return $this->hasMany(Inquiry::class, 'renter_id');
    }

    /**
     * HELPER: Check if the user is a Landlord
     */
    public function isLandlord()
    {
        return $this->role === 'landlord';
    }

    /**
     * HELPER: Check if the user is a Renter
     */
    public function isRenter()
    {
        return $this->role === 'renter';
    }
}