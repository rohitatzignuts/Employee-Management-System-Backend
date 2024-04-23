<?php

namespace App\Http\Controllers;

use App\Models\JobStatus;
use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

require_once app_path('Http/Helpers/APIResponse.php');
class JobStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $applications = JobStatus::all();
            $applications->transform(function ($applicant) {
                $company = Company::find($applicant->company_id);
                if ($company) {
                    $applicant->company_name = $company->name;
                } else {
                    $applicant->company_name = null;
                }
                return $applicant;
            });
            return ok('Applicants Found!', $applications);
        } catch (\Exception $e) {
            return error('Request failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function apply(Request $request)
    {
        try {
            $request->validate([
                'user_email' => 'required',
                'job_id' => 'required',
                'resume' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $user = User::where('email', $request['user_email'])->firstOrFail();
            $job = Job::findOrFail($request['job_id']);

            // Check if the user has already applied for this job
            $existingApplication = JobStatus::where('user_id', $user->id)
                ->where('job_id', $job->id)
                ->exists();

            if ($existingApplication) {
                return error('You have already applied for this job.');
            }

            $resumePath = $request->file('resume')->store('public/resumes');
            $resumePath = str_replace('public/', '', $resumePath);

            $jobApplication = JobStatus::create(
                $request->only(['job_id', 'user_email']) + [
                    'user_id' => $user->id,
                    'company_id' => $job->company_id,
                    'resume' => $resumePath,
                ],
            );
            return ok('Application successful!!', $jobApplication);
        } catch (\Exception $e) {
            return error('Request failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JobStatus $jobStatus)
    {
        try {
            //code...
        } catch (\Exception $e) {
            return error('Request failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobStatus $jobStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobStatus $jobStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobStatus $jobStatus)
    {
        //
    }
}
