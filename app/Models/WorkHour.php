<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'setting_id', 'hour_start', 'hour_end','payroll','is_vacation',
        
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workHour) {
            if (!$workHour->payroll) { // Si no ha sido definido pone por defecto mes actual y año

                $setting = Setting::where('id', $workHour->setting_id)->first();
                $jobName = $setting ? $setting->job_name : 'Unknown'; // Si no existe, usa un valor por defecto
                $workHour->payroll = now()->format('M-Y').'-'.$jobName; 
            }
        });

        static::updating(function ($workHour) {
            if (!$workHour->payroll) { // Si no ha sido definido pone por defecto mes actual y año

                $setting = Setting::where('id', $workHour->setting_id)->first();
                $jobName = $setting ? $setting->job_name : 'Unknown'; // Si no existe, usa un valor por defecto
                $workHour->payroll = now()->format('M-Y').'-'.$jobName; 
            }
        });
        
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }
}
