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
    public function index(Request $request)
    {
        try {
            $applications = JobStatus::all();
            $applications->transform(function ($applicant) {
                $company = Company::find($applicant->company_id);
                $job = Job::find($applicant->job_id);
                if ($company || $job) {
                    $applicant->company_name = $company->name ?? null;
                    $applicant->job_title = $job->title ?? null;
                }
                return $applicant;
            });
            if ($applications->isEmpty()) {
                return ok('No Applicants For Now !', []);
            }
            return ok('Applicants Found!', $applications);
        } catch (\Exception $e) {
            return error('Request failed: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function companyApplicants($id)
    {
        try {
            $applications = JobStatus::where('company_id', $id)->get();
            $applications->transform(function ($applicant) {
                $company = Company::find($applicant->company_id);
                $job = Job::find($applicant->job_id);
                if ($company || $job) {
                    $applicant->company_name = $company->name ?? null;
                    $applicant->job_title = $job->title ?? null;
                }
                return $applicant;
            });
            if ($applications->isEmpty()) {
                return ok('No Applicants For Now !', []);
            }
            return ok('Applicants Found!!', $applications);
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
    public function show($id)
    {
        try {
            $application = JobStatus::findOrFail($id);
            $company = Company::find($application->company_id);
            $job = Job::find($application->job_id);
            $application->company_name = $company->name ?? null;
            $application->job_title = $job->title ?? null;
            return ok('Application Found!!', $application);
        } catch (\Exception $e) {
            return error('Request failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update($id, Request $request)
    {
        try {
            $application = JobStatus::findOrFail($id);
            $application->update(['status' => $request->input('status')]);
            return ok('Application Status Updated!!', $application);
        } catch (\Exception $e) {
            return error('Request failed: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource on the basis of user email
     */
    public function applications(Request $request)
    {
        try {
            $user = User::where('email', $request->input('userEmail'))->firstOrFail();
            $applications = JobStatus::where('user_id', $user->id)->get();
            $applications->transform(function ($applicant) {
                $company = Company::find($applicant->company_id);
                $job = Job::find($applicant->job_id);
                if ($company || $job) {
                    $applicant->company_name = $company->name ?? null;
                    $applicant->company_logo = $company->logo ?? null;
                    $applicant->job_title = $job->title ?? null;
                    $applicant->job_location = $job->location ?? null;
                    $applicant->job_pay = $job->pay ?? null;
                }
                return $applicant;
            });
            return ok('Applicantions Found!', $applications);
        } catch (\Exception $e) {
            return error('Request failed: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        try {
            $application = JobStatus::findOrFail($id);

            // Check if delete type is provided in the request
            $deleteType = $request->input('deleteType');

            // Delete the company and associated users based on the delete type
            if ($deleteType === 'permanent') {
                // Permanently delete the company and associated users
                $application->forceDelete(); // Use forceDelete for permanent deletion
            } else {
                // Soft delete the company and associated users
                $application->delete();
            }
            return ok('application deleted successfully', 200);
        } catch (\Exception $e) {
            return error('Error deleting application: ' . $e->getMessage());
        }
    }
}
