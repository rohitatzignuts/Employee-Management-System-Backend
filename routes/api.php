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
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/login', [UserAuthController::class, 'login']);
Route::get('/jobs/search', [JobController::class, 'index']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/resetPassword', [UserAuthController::class, 'resetPassword']);
    Route::post('/logout', [UserAuthController::class, 'logout']);
    // company routes
    Route::middleware(['checkRole:admin'])->group(function () {
        Route::get('/companies/search', [CompanyController::class, 'index']);
        Route::get('/company/{id}', [CompanyController::class, 'show']);
        Route::post('/company/create', [CompanyController::class, 'store']);
        Route::delete('/company/{id}', [CompanyController::class, 'destroy']);
        Route::post('/company/update/{id}', [CompanyController::class, 'update']);
    });

    // company emoloyee routes
    Route::middleware(['checkRole:admin,cmp_admin'])->group(function () {
        Route::get('/employees/search', [CompanyEmployeeController::class, 'index']);
        Route::get('/{id}/employees/search', [CompanyEmployeeController::class, 'companyEmployees']);
        Route::get('/employee/{id}', [CompanyEmployeeController::class, 'show']);
        Route::post('/employee/create', [CompanyEmployeeController::class, 'store']);
        Route::post('/employee/update/{id}', [CompanyEmployeeController::class, 'update']);
        Route::delete('/employee/{id}', [CompanyEmployeeController::class, 'destroy']);
    });

    // jobs routes
    Route::middleware(['checkRole:admin,cmp_admin'])->group(function () {
        Route::get('/job/{id}', [JobController::class, 'show']);
        Route::get('{id}/jobs/search', [JobController::class, 'companyJobs']);
        Route::post('/job/create', [JobController::class, 'store']);
        Route::post('/job/update/{id}', [JobController::class, 'update']);
        Route::delete('/job/{id}', [JobController::class, 'destroy']);
    });
});
