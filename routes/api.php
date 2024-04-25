<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyEmployeeController;
use App\Http\Controllers\JobStatusController;

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
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/job/{id}', [JobController::class, 'show']);
Route::get('/registeredCompanies', [CompanyController::class, 'registeredCompanies']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/resetPassword', [UserAuthController::class, 'resetPassword']);
    Route::post('/logout', [UserAuthController::class, 'logout']);

    // company routes
    Route::middleware(['checkRole:admin'])->group(function () {
        Route::get('/companies', [CompanyController::class, 'index']);
        Route::get('/company/{id}', [CompanyController::class, 'show']);
        Route::post('/company/create', [CompanyController::class, 'store']);
        Route::delete('/company/{id}', [CompanyController::class, 'destroy']);
        Route::post('/company/update/{id}', [CompanyController::class, 'update']);
    });

    // company emoloyee routes
    Route::middleware(['checkRole:admin,cmp_admin'])->group(function () {
        Route::get('/employees', [CompanyEmployeeController::class, 'index']);
        Route::get('/{id}/employees', [CompanyEmployeeController::class, 'companyEmployees']);
        Route::get('/employee/{id}', [CompanyEmployeeController::class, 'show']);
        Route::post('/employee/create', [CompanyEmployeeController::class, 'store']);
        Route::post('/employee/update/{id}', [CompanyEmployeeController::class, 'update']);
        Route::delete('/employee/{id}', [CompanyEmployeeController::class, 'destroy']);
    });

    // jobs routes
    Route::middleware(['checkRole:admin,cmp_admin'])->group(function () {
        Route::get('{id}/jobs/search', [JobController::class, 'companyJobs']);
        Route::post('/job/create', [JobController::class, 'store']);
        Route::post('/job/update/{id}', [JobController::class, 'update']);
        Route::delete('/job/{id}', [JobController::class, 'destroy']);
    });

    // job status routes
    Route::middleware(['checkRole:admin,cmp_admin'])->group(function () {
        Route::get('/applications', [JobStatusController::class, 'index']);
        Route::get('/{id}/applications', [JobStatusController::class, 'companyApplicants']);
        Route::post('/application/edit-{id}', [JobStatusController::class, 'update']);
        Route::delete('/application/{id}', [JobStatusController::class, 'destroy']);
        Route::get('/application/{id}', [JobStatusController::class, 'show']);
    });
    Route::post('/job-{id}/apply', [JobStatusController::class, 'apply']);
    Route::get('/applications/status', [JobStatusController::class, 'applications']);
});
