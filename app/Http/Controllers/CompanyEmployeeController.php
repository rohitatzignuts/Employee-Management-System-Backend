<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyEmployee;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create employee. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_id)
    {
        try {
            $emp = CompanyEmployee::where('user_id', $user_id)->firstOrFail();
            return response()->json($emp);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $user_id)
    {
        $this->authorize('update',CompanyEmployee::class);
        try {
            $emp = CompanyEmployee::where('user_id', $user_id)->firstOrFail();
            $empData = $request->validate([
                'cmp_id' => 'required|integer',
                'joining_date' => 'required|date',
                'emp_number' => 'required|integer|unique:company_employees,emp_number,'.$user_id.',user_id',
            ]);
            $joiningDate = Carbon::createFromFormat('d/m/Y', $empData['joining_date'])->format('Y-m-d');
            $emp->update([
                'cmp_id' => $empData['cmp_id'],
                'joining_date' => $joiningDate,
                'emp_number' => $empData['emp_number'],
            ]);
            return response()->json([
                'message' => 'Employee Updated Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $user_id)
    {
        $this->authorize('delete', CompanyEmployee::class);
        try {
            $emp = CompanyEmployee::findOrFail($user_id);
            $emp->delete();
            return response()->json([
                'message' => 'Employee Deleted Successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Employee not found',
            ]);
        }
    }
}
