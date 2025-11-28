<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HourRegistered //Evento que contiene los datos necesarios para realizar los cálculos de nómina.
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $payroll;

    public function __construct($userId, $payroll)
    {
        $this->userId = $userId;
        $this->payroll = $payroll;
    }
}

