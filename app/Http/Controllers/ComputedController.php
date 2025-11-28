<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComputedHour;
use App\Models\WorkHour;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ComputedController
{
    public function selectPayroll(Request $request){ //Crea una nómina que el usuario haya seleccionado previamente
        

       $validator = Validator::make($request->all(),
       [
            'payroll' => 'required|string'
       ]);

       if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
        $user = Auth::user();
        $payroll= $request->input('payroll');

        $selectedPayroll = ComputedHour::where('user_id', $user->id)
            ->where('payroll', $request->payroll)
            ->first();

       if (!$selectedPayroll) {
        $selectedPayroll = ComputedHour::create([
            'user_id' => $user->id,
            'payroll' => $request->payroll,
        ]);
       }
       $this->computerHour($user->id, $payroll);
       return response()->json([
        'message' => 'Payroll selected correctly.',
        'data' => $selectedPayroll,
       ], 200);
    }

    public function loadComputerHour(Request $request) { //Carga los datos guardados en la base de datos tras su cómputo
        $user = Auth::user();

        $payroll = $request->query('payroll');

        if (!$payroll) {
            return response()->json(['nessage'=> 'Job name not found'], 404);
        }

        $computedHours = ComputedHour::where('user_id', $user->id)
            ->where('payroll', $payroll)
            ->first();

            if (!$computedHours){
                return response()->json([
                    'message' => 'No computed hours found or selected',
                ]);
            }
        return response()->json([
            'totalHours' => $computedHours->total_hour,
            'extraHours' => $computedHours->extra_hours,
            'grossSalary' => $computedHours->gross_salary,
            'netSalary' => $computedHours->net_salary,
            'nightHours' => $computedHours->total_night_hour,
        ]);
    }

    public function computerHour($userId, $payroll){ //Hace todos los cálculos necesarios para calcular el salario

        if (!$userId || !$payroll) {
            Log::error("Error: Missing userId or payroll", ['userId' => $userId, 'payroll' => $payroll]);
            return; // O cualquier otro tipo de manejo de error
            }


        $workHours = WorkHour::where('payroll', $payroll)
        ->get();

        $settingId = WorkHour::where('payroll', $payroll)
        ->where('user_id', $userId)
        ->pluck('setting_id')->first();

        Log::debug('Setting ID obtenido para cálculo:', [
            'user_id' => $userId,
            'payroll' => $payroll,
            'setting_id' => $settingId
        ]);

        $calculatedTotalHours = 0;
        $calculatedNightHours = 0;
        $calculatedExtraHours = 0;
        $calculatedVacationDay = 0;

        $setting = Setting::where('id', $settingId)
        ->select('salary_per_hour', 'contract_hour', 'night_hours_start', 'night_hours_end', 
            'night_salary', 'extra_salary', 'salary_extra_hours', 'tax_percentage')
        ->first();

       $salaryPerHour = $setting->salary_per_hour;
       $contractHour = $setting->contract_hour;
       $nightHoursStart = $setting->night_hours_start;
       $nightHoursEnd = $setting->night_hours_end;
       $nightSalary = $setting->night_salary;
       $extraSalary = $setting->extra_salary;
       $salaryExtraHours = $setting->salary_extra_hours;
       $taxPercentage = $setting->tax_percentage;

        foreach($workHours as $workHour){ //Calcula todas las horas recorriendo cada registro
                
            if ($workHour->hour_start && $workHour->hour_end){
                $startTime = Carbon::parse($workHour->hour_start);
                $endTime = Carbon::parse($workHour->hour_end);
                
                if ($workHour->is_vacation) { //Calcula los días de vacaciones
                    $hourPerDayVacation = ($contractHour / 4) / 7;
                    $calculatedVacationDay = $startTime->diffInDays($endTime);
                    $calculatedTotalHours += $hourPerDayVacation * $calculatedVacationDay;
                    
                    Log::info('Vacation day added:', ['vacationDays' => $calculatedVacationDay, 'Total Hour' => $calculatedTotalHours]);

            } else {
                $workedHours = $startTime->diffInHours($endTime);

                $calculatedTotalHours += $workedHours;

                Log::info('Total worked hours:', ['Total hours' => $calculatedTotalHours]);

                if (!is_null($nightHoursStart) && !is_null($nightHoursEnd)) {
                $nightHoursStartFormatted = Carbon::parse($nightHoursStart); // Convertimos en Carbon las horas para asegurarnos que el cálculo sea correcto
                $nightHoursEndFormatted = Carbon::parse($nightHoursEnd);
                $nightHoursStartFormatted = Carbon::parse($startTime->toDateString() . ' ' . $nightHoursStartFormatted->format('H:i:s'));
                $nightHoursEndFormatted = Carbon::parse($endTime->toDateString() . ' ' . $nightHoursEndFormatted->format('H:i:s'));
                if ($startTime->toDateString() === $endTime->toDateString()) {
                    $nightHoursEndFormatted->addDay();
                }
              
                $nightHoursWorked = 0;

                // Determina la intersección entre el periodo trabajado y las horas nocturnas
                if ($startTime->lessThan($nightHoursStartFormatted) && $endTime->greaterThan($nightHoursStartFormatted)) {
                    // Caso 1: Empieza antes del período nocturno pero termina dentro de él
                    $nightStart = $nightHoursStartFormatted;
                    $nightEnd = min($endTime, $nightHoursEndFormatted);
                } elseif ($startTime->between($nightHoursStartFormatted, $nightHoursEndFormatted)) {
                    // Caso 2: Empieza dentro del período nocturno
                    $nightStart = $startTime;
                    $nightEnd = min($endTime, $nightHoursEndFormatted);
                } else {
                    // No hay intersección con las horas nocturnas
                    $nightStart = null;
                    $nightEnd = null;
                }
                
                // Si hay intersección, calculamos la diferencia
                if ($nightStart && $nightEnd) {
                    $nightHoursWorked = $nightStart->diffInMinutes($nightEnd) / 60;
                    $calculatedNightHours += $nightHoursWorked;
                }
                // Podemos ver en el log que los cálculos son correctos (Utilizado para pruebas)
                Log::info('Night hours calculation:', [
                    'calculatedNightHours' => $calculatedNightHours,
                    'NightHoursStart' => $nightHoursStartFormatted,
                    'Night End' => $nightHoursEndFormatted,
                    'Start Time' => $startTime,
                    'End Time' => $endTime,
                    'Night Hours Worked' => $nightHoursWorked
                ]);
                }else{
                    $nightHoursWorked= 0;
                }     
            }
              
            }
        }

            if ($calculatedTotalHours > $contractHour) { //Extraemos las horas extras
                $calculatedExtraHours += $calculatedTotalHours - $contractHour;
                $calculatedTotalHours = $calculatedTotalHours - $calculatedExtraHours;
                Log::info('Total worked hours:', ['Total hours' => $calculatedTotalHours, 'Total Extra hours:' => $calculatedExtraHours]);
            }

        
        //Realizamos los cálculos finales
        $calculatedTotalSalary = $calculatedTotalHours * $salaryPerHour;
        $extraHoursSalary = $calculatedExtraHours * $salaryExtraHours;
        $calculatedNightSalary = $calculatedNightHours * $nightSalary;


        $grossSalary = $calculatedTotalSalary + $extraHoursSalary + $calculatedNightSalary + $extraSalary;
        $netSalary = $grossSalary - ($grossSalary * ($taxPercentage / 100));



        Log::info('Final calculated :', [
            'Total Salary' => $calculatedTotalSalary,
            'Extra Salary' => $extraHoursSalary,
            'Night Salary' => $calculatedNightSalary,
            'Gross Salary' => $grossSalary,
            'Net Salary' => $netSalary
        ]);
        

        ComputedHour::where('user_id', $userId) //Actualizamos los valores.
        ->where('payroll', $payroll)
        ->update([
        'total_hour' => $calculatedTotalHours,
        'total_night_hour' => $calculatedNightHours,
        'extra_hours' => $calculatedExtraHours,
        'gross_salary' => $grossSalary,
        'net_salary' => $netSalary
    ]);
    
       return;

    }
}
