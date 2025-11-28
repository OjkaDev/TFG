<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkHour;
use App\Models\Setting;
use App\Events\HourRegistered;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class TrackingController
{
    public function trackingsave(Request $request){ //Guarda un registro de horas
        
        $validator = Validator::make($request->all(),
        [
            'jobname' => 'required|string|max:255',
            'start'=> 'required|date_format:Y-m-d\TH:i',
            'end'=> 'required|date_format:Y-m-d\TH:i',
            'payroll'=> 'nullable|string|max:255',
            'isVacation'=>'required|boolean',
            
        ]);

    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $settings = Setting::where('job_name', $request->jobname)->first();

        if(!$settings) {
            return response()->json(['error' => 'Job settings not found'], 404);
        }

        try{
       
        $time = WorkHour::Create([

            'user_id' => $user->id,
            'job_name' => $request->jobname,
            'setting_id'=> $settings->id,
            'hour_start'=> $request->start,
            'hour_end' => $request->end,
            'payroll' => $request->payroll,
            'is_vacation' => $request->isVacation,

        ]);
        $time->refresh();
        Log::info('Tracking hour saved successfully', [
            'time' => $time
        ]);

        event(new HourRegistered($user->id, $time->payroll)); //Llama a la función computerHour en TrackingController

        return response()->json([
            'message' => 'Time saved successfully',
            'settings' => $settings
        ], 201);
    } catch (QueryException $e) {
        Log::error('Error saving work hour:', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to save work hour'], 500);
    }
    }

    public function trackingupdate(Request $request, $id) //Actualiza el registro de horas si el usuario lo ha editado
    {
        $workHour = WorkHour::findorFail($id);

        $hourStart = Carbon::parse($request->start)->format('Y-m-d\TH:i'); //Adapta al formato de las horas del Validate
        $hourEnd = Carbon::parse($request->end)->format('Y-m-d\TH:i');
        $request->merge(['start' => $hourStart, 'end' => $hourEnd]);

        $request->validate([
            'jobname' => 'required|string|max:255',
            'start'=> 'required|date_format:Y-m-d\TH:i',
            'end'=> 'required|date_format:Y-m-d\TH:i',
            'payroll'=> 'nullable|string|max:255',
            'isVacation'=>'required|boolean',
        ]);

        $settings = Setting::where('job_name', $request->jobname)->first();
        $user = Auth::user();

        if(!$settings) {
            return response()->json(['error' => 'Job settings not found'], 404);
        }

        $workHour->update([
            'setting_id'=> $settings->id,
            'hour_start' => $hourStart,
            'hour_end' => $hourEnd,
            'payroll' => $request->payroll,
            'is_vacation' => $request->isVacation,
        ]);

        $workHour->refresh();
        event(new HourRegistered($user->id, $workHour->payroll));



        return response()->json([
            'message' => 'Time updated successfully',
            'settings' => $settings
        ], 200);
    }

    public function loadworkhours() { //Carga los registros de horas
        $user = Auth::user();
        $workHours = WorkHour::where('user_id', $user->id)
            ->with('setting:id,job_name')
            ->get();
        if ($workHours->isEmpty()) {
            return response()->json(['message'=> 'Work hours not found'], 404);
        }
        return response()->json($workHours);
    }

    public function trackingdelete(Request $request) { //Elimina el registro de horas.
            Log::info('Request recibido en trackingdelete', [
            'id' => $request->id,
            'user_id' => $request->input('user_id'),
            'payroll' => $request->input('payroll'),
            'completo' => $request->all()
        ]);
        $setting = WorkHour::where('id', $request->id) ->first();
        if (!$setting) {
            return response()->json(['message' => 'Time not found'], 404);
        }
        
        $setting->delete();

       
        event(new HourRegistered($request->input('user_id'), $request->input('payroll')));

        return response()->json(['message' => 'Time deleted succesfully'], 200);

    }

    public function loadpayroll() { //Carga el nombre de las Payrrolls y JobName junto a sus ids
        $user = Auth::user();

        $payroll = WorkHour::where('work_hours.user_id', $user->id)
        ->join('settings', 'work_hours.setting_id', '=', 'settings.id') // Unimos con settings
        ->select('work_hours.payroll', 'settings.job_name') // Seleccionamos payroll y job_name
        ->distinct() // Evitamos duplicados
        ->get();

        if (!$payroll) {
            return response()->json(['message' => 'Payroll not found'], 404);
        }
        

        return response()->json($payroll->values());
    }
}

