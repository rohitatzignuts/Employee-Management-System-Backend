<?php

namespace App\Http\Controllers;

use App\Models\User;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // $company_id = $request->input('company_id');
            $employeeNumber = $this->employeeService->generateUniqueEmployeeNumber(4);
            $cmpEmployee = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'joining_date' => 'required|date',
                'company_id' => 'required',
            ]);

            $employee = User::create([
                'first_name' => $cmpEmployee['first_name'],
                'last_name' => $cmpEmployee['last_name'],
                'email' => $cmpEmployee['email'],
                'role' => 'employee',
                'password' => bcrypt($cmpEmployee['password']),
                'joining_date' => $cmpEmployee['joining_date'],
                'company_id' => 4,
                'emp_number' => $employeeNumber,
            ]);

            $preference = Preferences::updateOrCreate([
                'code' => 'EMP',
                'value' => (int) substr($employeeNumber, 4),
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
