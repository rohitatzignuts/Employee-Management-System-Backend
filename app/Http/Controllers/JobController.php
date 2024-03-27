<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Job;
use Illuminate\Http\Request;

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
        $this->authorize('create',Job::class);
        $jobData = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'pay' => 'required|string',
            'cmp_id' => 'required|string',
            'is_active' => ['required', 'integer', Rule::in([0, 1])],
            'is_trending' => ['required', 'integer', Rule::in([0, 1])],
        ]);
        $job = Job::create($jobData);
        return response()->json([
            'message' => 'Job Created Successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = Job::find($id);
        return response()->json($job);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update',Job::class);
        $job = Job::find($id);
        $job->update($request->all());
        return $job;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete',Job::class);
        $job = Job::find($id);
        $job->delete();
        return response()->json([
            'message' => 'Job Deleted Successfully',
        ]);
    }
    /**
     * Search for a name
     */
    public function search(string $title)
    {
        return job::where('title', 'like', '%'.$title.'%')->get();
    }
}
