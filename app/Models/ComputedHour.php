<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputedHour extends Model
{
    protected $fillable = [
        'user_id', 'payroll', 'total_hour', 'total_night_hour', 'extra_hours',
        'gross_salary', 'net_salary'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
