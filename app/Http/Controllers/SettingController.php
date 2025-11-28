<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;




class SettingController
{
    public function setting(Request $request){ //Crea o actualiza la setting creada por el usuario
        
        $validator = Validator::make($request->all(),
        [
          'jobname' => 'required|string|max:255',
          'salaryperhour'=> 'required|numeric|max:255',
          'totalcontracthour'=> 'required|numeric|min:1',
          'nightstart'=> 'nullable|date_format:H:i',
          'nightend'=> 'nullable|date_format:H:i',
          'nightsalary'=> 'nullable|numeric',
          'extrasalary'=> 'nullable|numeric',
          'extrahours'=> 'nullable|numeric',
          'taxpercentage'=> 'required|numeric|min:0|max:100',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $user = Auth::user();
    
        $settings = Setting::updateOrCreate(
            [
                'user_id' => $user->id,
                'job_name' => $request->jobname,
            ],
            [
                'salary_per_hour' => $request->salaryperhour,
                'contract_hour' => $request->totalcontracthour,
                'night_hours_start' => $request->nightstart,
                'night_hours_end' => $request->nightend,
                'night_salary' => $request->nightsalary,
                'extra_salary' => $request->extrasalary,
                'salary_extra_hours'=> $request->extrahours,
                'tax_percentage' => $request->taxpercentage,
            ]);
    
        return response()->json([
            'message' => 'Settings saved successfully',
            'settings' => $settings
        ], 201);
    }

    public function getJobNames() { //Toma el Jobname
        $user = Auth::user();
        $jobNames = Setting::where('user_id', $user->id)->select('job_name')->get();
        if (!$jobNames) {
            return response()->json(['message'=> 'JobName not found'], 404);
        }
        return response()->json($jobNames);
    }

    public function getSetting($jobName) { //Toma los valores de Setting
        $user = Auth::user();
        $setting = Setting::where('user_id', $user->id)->where('job_name', $jobName)->first();
        if (!$setting) {
            return response()->json(['message'=> 'Setting not found'], 404);
        }
        return response()->json($setting);
    }

    public function deleteSetting($jobName) { //Elimina una setting seleccionada por el usuario

        $user = Auth::user();
        $setting = Setting::where('user_id', $user->id)->where('job_name', $jobName) ->first();

        if (!$setting) {
            return response()->json(['message' => 'Setting not found'], 404);
        }

        $setting->delete();
        return response()->json(['message' => 'Setting deleted succesfully'], 200);
    }
}
