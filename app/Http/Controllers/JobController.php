<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
require_once app_path('Http/Helpers/APIResponse.php');

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('term')) {
                $term = $request->input('term');
                $jobs = Job::with('company')
                    ->where('title', 'like', '%' . $term . '%')
                    ->get();
            } else {
                $jobs = Job::all();
            }

            $jobs->transform(function ($job) {
                $job->company_name = $job->company->name;
                unset($job->company);
                return $job;
            });

            if ($jobs->isEmpty()) {
                return [];
            }
            return $jobs;
        } catch (\Exception $e) {
            return error('Error getting jobs: ' . $e->getMessage());
        }
    }
    /**
     * Display a listing of the resource by company.
     */
    public function companyJobs(string $id, Request $request)
    {
        try {
            if ($request->has('term')) {
                $term = $request->input('term');
                $jobs = Job::where('company_id', $id)
                    ->where('title', 'like', '%' . $term . '%')
                    ->get();
            } else {
                $jobs = Job::where('company_id', $id)->get();
            }

            $jobs->transform(function ($job) {
                $job->company_name = $job->company->name;
                unset($job->company);
                return $job;
            });

            if ($jobs->isEmpty()) {
                return [];
            }
            return $jobs;
        } catch (\Exception $e) {
            return error('Error getting jobs: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'location' => 'required|string',
                'pay' => 'required|string',
            ]);
            $company_id = Company::where('name', $request['company_name'])->first()->id;
            $job = Job::create($request->only(['title', 'description', 'location', 'pay']) + ['created_by' => auth()->user()->id] + ['company_id' => $company_id]);
            return ok('Job Created Successfully', $job, 200);
        } catch (\Exception $e) {
            return error('Error Creating Job : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $job = Job::findOrFail($id);
            $job->company_name = $job->company->name;
            $job->makeHidden('company');
            return response()->json($job);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'message' => 'Job not found',
                ],
                404,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $job = Job::findOrFail($id);
            $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'location' => 'required|string',
                'pay' => 'required|string',
            ]);
            $job->update($request->all());
            return ok('Job Updated Successfully', $job, 200);
        } catch (\Exception $e) {
            return error('Error Updating Job : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $job = Job::findOrFail($id);

            // Check if delete type is provided in the request
            $deleteType = $request->input('deleteType');

            // Delete the company and associated users based on the delete type
            if ($deleteType === 'permanent') {
                // Permanently delete the company and associated users
                $job->forceDelete(); // Use forceDelete for permanent deletion
            } else {
                // Soft delete the company and associated users
                $job->delete();
            }
            return ok('Job deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return error('Error deleting Job: ' . $e->getMessage());
        }
    }

    /**
     * Search for a name
     */
    public function search(string $title)
    {
        try {
            return Job::where('title', 'like', '%' . $title . '%')->get();
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
