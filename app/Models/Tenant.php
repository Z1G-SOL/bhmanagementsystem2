<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Tenant extends Model
{
    protected $fillable = [
        'room_id', 'user_id', 'name', 'contact_number', 'photo_path', 'move_in_date',
    ];

    protected $casts = [
        'move_in_date' => 'date',
    ];

    public function room(): BelongsTo { return $this->belongsTo(Room::class); }
    public function landlord(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }

    public function getMonthsAccruedAttribute(): int
    {
        $months = ceil($this->move_in_date->diffInMonths(Carbon::now()));
        return $months <= 0 ? 1 : (int) $months;
    }

    public function getTotalOwedAttribute(): float { return (float) ($this->months_accrued * $this->room->monthly_rate); }
    public function getTotalPaidAttribute(): float { return (float) $this->payments()->sum('amount_paid'); }
    public function getBalanceOwedAttribute(): float { return (float) ($this->total_owed - $this->total_paid); }

    public function getIsRentOverdueAttribute(): bool
    {
        if ($this->balance_owed <= 0) return false;
        $dayOfMoveIn = $this->move_in_date->day;
        $currentDay = Carbon::now()->day;
        return $currentDay >= $dayOfMoveIn || $this->move_in_date->diffInMonths(Carbon::now()) >= 1;
    }
}