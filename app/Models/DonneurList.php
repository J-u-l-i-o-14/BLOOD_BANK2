<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonneurList extends Model
{
    use HasFactory;

    protected $table = 'donneur_list';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birthdate',
        'blood_type_id',
    ];

    public function bloodType()
    {
        return $this->belongsTo(BloodType::class, 'blood_type_id');
    }
}
