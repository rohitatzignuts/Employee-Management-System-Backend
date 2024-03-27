<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyEmployeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// public routes
Route::get('/companies',[CompanyController::class,'index']);
Route::post('/register',[UserAuthController::class,'register']);
Route::post('/login',[UserAuthController::class,'login']);
Route::get('/jobs',[JobController::class,'index']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']],function () {
    Route::post('/logout',[UserAuthController::class,'logout']);
    // company routes
    Route::get('/company/{id}',[CompanyController::class,'show']);
    Route::post('/company/create',[CompanyController::class,'store']);
    Route::delete('/company/{id}',[CompanyController::class,'destroy']);
    Route::put('/company/update/{id}',[CompanyController::class,'update']);
    Route::get('/company/search/{name}', [CompanyController::class, 'search']);
    // company emoloyee routes
    Route::get('/employees',[CompanyEmployeeController::class,'index']);
    Route::get('/employee/{id}',[CompanyEmployeeController::class,'show']);
    Route::post('/employee/create',[CompanyEmployeeController::class,'store']);
    Route::put('/employee/update/{id}',[CompanyEmployeeController::class,'update']);
    Route::delete('/employee/{id}',[CompanyEmployeeController::class,'destroy']);
    // jobs routes
    Route::get('/job/{id}',[JobController::class,'show']);
    Route::post('/job/create',[JobController::class,'store']);
    Route::put('/job/update/{id}',[JobController::class,'update']);
    Route::delete('/job/{id}',[JobController::class,'destroy']);
    Route::get('/job/search/{title}', [JobController::class, 'search']);
});
