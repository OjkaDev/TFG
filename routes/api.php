<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\ComputedController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']) -> name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully']);
}); 

Route::middleware('auth:sanctum')->get('/dashboard', function () {
    return response()->json(['message' => 'Welcome to your dashboard']);
});

Route::middleware('auth:sanctum')->post('/settings', [SettingController::class, 'setting']);
Route::middleware('auth:sanctum')->get('settings/job-names', [SettingController::class, 'getJobNames']);
Route::middleware('auth:sanctum')->get('settings/{jobName}', [SettingController::class, 'getSetting']);
Route::middleware('auth:sanctum')->put('/settings/{jobName}', [SettingController::class, 'setting']);
Route::middleware('auth:sanctum')->delete('/settings/{jobName}', [SettingController::class, 'deleteSetting']);


Route::middleware('auth:sanctum')->post('/trackingsave', [TrackingController::class, 'trackingsave']);
Route::middleware('auth:sanctum')->get('/loadworkhours',[TrackingController::class, 'loadworkhours']);
Route::middleware('auth:sanctum')->put('/trackingupdate/{id}',[TrackingController::class, 'trackingupdate']);
Route::middleware('auth:sanctum')->post('/trackingdelete',[TrackingController::class, 'trackingdelete']);
Route::middleware('auth:sanctum')->get('/loadpayroll',[TrackingController::class, 'loadpayroll']);


Route::middleware('auth:sanctum')->post('/selectPayroll', [ComputedController::class, 'selectPayroll']);
Route::middleware('auth:sanctum')->get('/computedHours', [ComputedController::class, 'loadComputerHour']);