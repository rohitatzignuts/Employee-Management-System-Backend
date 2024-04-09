<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Preferences;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
require_once app_path('Http/Helpers/APIResponse.php');

class CompanyEmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = User::whereIn('role', ['cmp_admin', 'employee'])->get();

        foreach ($employees as $employee) {
            $employee->company_name = $employee->company->name;
        }

        return $employees;
    }

    /**
     * Display a listing of the resource by companyId
     */
    public function companyEmployees(string $id)
    {
        $company = Company::findOrFail($id);
        // Get the employees of the company
        $employees = User::where('company_id', $company->id)
        ->get();
        return $employees;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $cmpEmployee = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'joining_date' => 'required|date',
                'company_name' => 'required|string',
            ]);
            $company = Company::where('name', $cmpEmployee['company_name'])->first();
            $employeeNumber = $this->employeeService->generateUniqueEmployeeNumber($company->id);
            $employee = User::create([
                'first_name' => $cmpEmployee['first_name'],
                'last_name' => $cmpEmployee['last_name'],
                'email' => $cmpEmployee['email'],
                'role' => 'employee',
                'password' => bcrypt($cmpEmployee['password']),
                'joining_date' => $cmpEmployee['joining_date'],
                'company_id' => $company->id,
                'emp_number' => $employeeNumber,
            ]);

            $preference = Preferences::updateOrCreate([
                'code' => 'EMP',
                'value' => (int) substr($employeeNumber, $company->id),
            ]);
            return ok('Employee Registered Successfully', $employee);
        } catch (\Exception $e) {
            return error('Error Registering Employee: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $employee = User::findOrFail($id);
            return response()->json([
                'employee' => $employee,
            ]);
        } catch (ModelNotFoundException $e) {
            return error('Error Finding employee: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $employee = User::findOrFail($id);
            $cmpEmployee = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => ['required', 'string', 'email', Rule::unique('users')->ignore($id)],
                'joining_date' => 'required|date',
            ]);

            $employee->update([
                'first_name' => $cmpEmployee['first_name'],
                'last_name' => $cmpEmployee['last_name'],
                'email' => $cmpEmployee['email'],
                'joining_date' => $cmpEmployee['joining_date'],
            ]);
            return ok('Employee Updated Successfully', $employee);
        } catch (\Exception $e) {
            return error('Error Updating Employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        try {
            $user = User::findOrFail($id);

            // Check if delete type is provided in the request
            $deleteType = $request->input('deleteType');

            // Delete the Employee based on the delete type
            if ($deleteType === 'permanent') {
                // Permanently delete the Employee
                $user->forceDelete(); // Use forceDelete for permanent deletion
            } else {
                // Soft delete the Employee
                $user->delete();
            }
            return ok('Employee deleted successfully');
        } catch (ModelNotFoundException $e) {
            return error('Error deleting Employee: ' . $e->getMessage());
        }
    }
}
