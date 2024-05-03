<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyEmployeeController;
use App\Http\Controllers\AnnouncementController;
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
Route::get('/jobs/{job_id}', [JobController::class, 'show']);
Route::get('/registeredCompanies', [CompanyController::class, 'registeredCompanies']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/changePassword', [UserAuthController::class, 'changePassword']);
    Route::post('/logout', [UserAuthController::class, 'logout']);

    Route::group(['prefix' => 'announcements', 'middleware' => 'checkRole:admin'], function () {
        Route::post('/create', [AnnouncementController::class, 'store']);
        Route::post('/update/{announcement_id}', [AnnouncementController::class, 'update']);
        Route::delete('/{announcement_id}', [AnnouncementController::class, 'delete']);
    });

    //company routes
    Route::group(['prefix' => 'companies', 'middleware' => 'checkRole:admin,cmp_admin'], function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::get('/{id}', [CompanyController::class, 'show']);
        Route::post('/create', [CompanyController::class, 'store']);
        Route::delete('/{id}', [CompanyController::class, 'destroy']);
        Route::post('/update/{id}', [CompanyController::class, 'update']);
    });

    // company emoloyee routes
    Route::group(['prefix' => 'companies', 'middleware' => 'checkRole:admin,cmp_admin'], function () {
        Route::get('/employees/all', [CompanyEmployeeController::class, 'index']);
        Route::get('/{company_id}/employees', [CompanyEmployeeController::class, 'companyEmployees']);
        Route::get('/employees/{employee_id}', [CompanyEmployeeController::class, 'show']);
        Route::post('/employees/create', [CompanyEmployeeController::class, 'store']);
        Route::post('/employees/update/{employee_id}', [CompanyEmployeeController::class, 'update']);
        Route::delete('/employees/{employee_id}', [CompanyEmployeeController::class, 'destroy']);

        // jobs routes
        Route::get('{company_id}/jobs', [JobController::class, 'companyJobs']);
        Route::post('/jobs/create', [JobController::class, 'store']);
        Route::post('/jobs/update/{job_id}', [JobController::class, 'update']);
        Route::delete('/jobs/{job_id}', [JobController::class, 'destroy']);

        // job status routes
        Route::get('/applications', [JobStatusController::class, 'index']);
        Route::get('/{company_id}/applications', [JobStatusController::class, 'companyApplicants']);
        Route::post('/applications/{application_id}/edit', [JobStatusController::class, 'update']);
        Route::delete('/applications/{applicatiob_id}', [JobStatusController::class, 'destroy']);
        Route::get('/applications/{application_id}', [JobStatusController::class, 'show']);
    });

    Route::group(['prefix' => 'announcements', 'middleware' => 'checkRole:admin'], function () {
        Route::get('/all', [AnnouncementController::class, 'index']);
        Route::get('/{announcement_id}', [AnnouncementController::class, 'show']);
    });

    Route::post('/job/{id}/apply', [JobStatusController::class, 'apply']);
    Route::get('/applications/status', [JobStatusController::class, 'applications']);
});
