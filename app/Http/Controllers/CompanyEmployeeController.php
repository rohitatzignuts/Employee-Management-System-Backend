<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyEmployee;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CompanyEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CompanyEmployee::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create',CompanyEmployee::class);
        $empData =  $request->validate([
            'user_id' => 'required|integer',
            'cmp_id' => 'required|integer',
            'joining_date' => 'required|date',
            'emp_number' => 'required|integer|unique:company_employees,emp_number',
        ]);
        $joiningDate = Carbon::createFromFormat('d/m/Y', $empData['joining_date'])->format('Y-m-d');

        $emp = CompanyEmployee::create([
            'user_id' => $empData['user_id'],
            'cmp_id' => $empData['cmp_id'],
            'joining_date' => $joiningDate,
            'emp_number' => $empData['emp_number'],
        ]);

        return response()->json([
            'message' => 'Employee Created Successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_id)
    {
        $emp = CompanyEmployee::where('user_id', $user_id)->firstOrFail();
        return response()->json($emp);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $user_id)
    {
        $this->authorize('update',CompanyEmployee::class);
        $emp = CompanyEmployee::where('user_id', $user_id)->firstOrFail();
        $joiningDate = Carbon::createFromFormat('d/m/Y', $request->joining_date)->format('Y-m-d');
        $emp->update([
            'cmp_id' => $request->cmp_id,
            'joining_date' => $joiningDate,
            'emp_number' => $request->emp_number,
        ]);
        return $emp;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $user_id)
    {
        $this->authorize('delete',CompanyEmployee::class);
        $emp = CompanyEmployee::where('user_id', $user_id)->firstOrFail();
        $emp->delete();
        return response()->json([
            'message' => 'Employee Deleted Successfully',
        ]);
    }

}
