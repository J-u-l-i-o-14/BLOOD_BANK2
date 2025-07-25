<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'center_id', 'blood_type_id', 'type', 'message', 'resolved'
    ];

    protected $casts = [
        'resolved' => 'boolean',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function bloodType()
    {
        return $this->belongsTo(BloodType::class);
    }

    // Scopes
    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('resolved', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCenter($query, $centerId)
    {
        return $query->where('center_id', $centerId);
    }

    public function scopeByBloodType($query, $bloodTypeId)
    {
        return $query->where('blood_type_id', $bloodTypeId);
    }
} 