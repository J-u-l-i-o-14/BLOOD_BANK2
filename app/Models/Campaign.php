<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'center_id', 'name', 'title', 'description', 'location', 'date', 'end_date'
    ];

    protected $casts = [
        'date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function donationHistories()
    {
        return $this->hasMany(DonationHistory::class);
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', Carbon::now());
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', Carbon::now());
    }

    public function scopeByCenter($query, $centerId)
    {
        return $query->where('center_id', $centerId);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        $now = Carbon::now();
        return $this->date <= $now && (!$this->end_date || $this->end_date >= $now);
    }

    public function getIsUpcomingAttribute()
    {
        return $this->date > Carbon::now();
    }

    public function getIsPastAttribute()
    {
        return $this->date < Carbon::now();
    }
}