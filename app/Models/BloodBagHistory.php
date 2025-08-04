<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodBagHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'blood_bag_id',
        'type',
        'description',
        'user_id',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the blood bag that owns the history record.
     */
    public function bloodBag(): BelongsTo
    {
        return $this->belongsTo(BloodBag::class);
    }

    /**
     * Get the user that created the history record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
