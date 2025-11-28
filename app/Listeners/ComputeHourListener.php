<?php

namespace App\Listeners;

use App\Events\HourRegistered;
use App\Http\Controllers\ComputedController;

class ComputeHourListener
{
    public function handle(HourRegistered $event)  //Escucha al evento HourRegistered, instanciando ComputedController y llama al método computerHour()
    {
        $computedController = new ComputedController();
        $computedController->computerHour($event->userId, $event->payroll);
    }
}


