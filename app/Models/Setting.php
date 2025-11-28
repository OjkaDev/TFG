<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_name', 'user_id', 'salary_per_hour', 'contract_hour',
        'night_hours_start', 'night_hours_end', 'night_salary',
        'extra_salary','salary_extra_hours','tax_percentage'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function WorkHour()
    {
        return $this->hasMany(WorkHour::class);
    }
}

