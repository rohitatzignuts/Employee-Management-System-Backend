<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Job::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Job::class);
        try {
            $jobData = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'pay' => 'required|string',
                'cmp_id' => 'required|string',
                'is_active' => ['required', 'integer', Rule::in([0, 1])],
                'is_trending' => ['required', 'integer', Rule::in([0, 1])],
            ]);
            $job = Job::create($jobData);
            return response()->json([
                'message' => 'Job Created Successfully',
            ]);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'message' => 'Failed to create job: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $job = Job::findOrFail($id);
            return response()->json($job);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Job not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update', Job::class);
        try {
            $job = Job::findOrFail($id);
            $job->update($request->all());
            return $job;
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Job not found',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Job::class);
        try {
            $job = Job::findOrFail($id);
            $job->delete();
            return response()->json([
                'message' => 'Job Deleted Successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Job not found',
            ], 404);
        }
    }

    /**
     * Search for a name
     */
    public function search(string $title)
    {
        try {
            return Job::where('title', 'like', '%'.$title.'%')->get();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
