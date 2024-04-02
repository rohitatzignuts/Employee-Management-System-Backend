<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\CompanyEmployee;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\EmployeeService;

require_once app_path('Http/Helpers/APIResponse.php');

class CompanyController extends Controller
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
        return Company::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $companyData = $request->validate([
                'name' => 'required|string',
                'logo' => 'mimes:jpg,jpeg,png|max:2048',
                'website' => 'required|string',
                'cmp_email' => 'required|string|email|unique:companies',
                'location' => 'required|string',
                'cmp_admin_first_name' => 'required|string',
                'cmp_admin_last_name' => 'required|string',
                'cmp_admin_email' => 'required|email|unique:users,email',
                'cmp_admin_password' => 'required|min:8',
                'cmp_admin_joining_date' => 'required|date',
            ]);

            // Save the uploaded logo file
            $logoPath = $request->file('logo')->store('public/logos');

            $company = Company::create([
                'name' => $companyData['name'],
                'logo' => $logoPath,
                'website' => $companyData['website'],
                'cmp_email' => $companyData['cmp_email'],
                'location' => $companyData['location'],
            ]);

            $user = User::create([
                'first_name' => $companyData['cmp_admin_first_name'],
                'last_name' => $companyData['cmp_admin_last_name'],
                'email' => $companyData['cmp_admin_email'],
                'role' => 'cmp_admin',
                'password' => bcrypt($companyData['cmp_admin_password']),
            ]);

            $data = [
                'joining_date' => $companyData['cmp_admin_joining_date'],
                'emp_number' => $this->employeeService->generateUniqueEmployeeNumber(),
                'user_id' => $user->id,
            ];

            $company->companyEmployee()->create($data);

            return ok('Company Created Successfully', $company);
        } catch (\Exception $e) {
            return error('Error creating company: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $company = Company::findOrFail($id);
            $employee = $company->companyEmployee()->first();
            $user = $employee->user;

            return response()->json([
                'company' => $company,
                'employee' => $employee,
            ]);
        } catch (ModelNotFoundException $e) {
            return error('Error Finding company: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $company = Company::findOrFail($id);
            $companyEmployee = $company->companyEmployee()->first();
            $user = $companyEmployee->user;

            $companyData = $request->validate([
                'name' => 'required|string',
                'website' => 'required|string',
                'cmp_email' => ['required', 'string', 'email', Rule::unique('companies')->ignore($id)],
                'location' => 'required|string',
                'is_active' => 'sometimes|integer',
                'cmp_admin_first_name' => 'required|string',
                'cmp_admin_last_name' => 'required|string',
                'cmp_admin_email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
                'cmp_admin_joining_date' => 'required|date',
            ]);

            // Update the company data
            $company->update([
                'name' => $companyData['name'],
                'website' => $companyData['website'],
                'cmp_email' => $companyData['cmp_email'],
                'location' => $companyData['location'],
                'is_active' => $companyData['is_active'],
            ]);

            // Update or create the user (admin) data
            $user->update([
                'first_name' => $companyData['cmp_admin_first_name'],
                'last_name' => $companyData['cmp_admin_last_name'],
                'email' => $companyData['cmp_admin_email'],
            ]);

            $data = [
                'joining_date' => $companyData['cmp_admin_joining_date'],
            ];

            // Update or create the company employee data
            $company->companyEmployee()->update($data);

            return response()->json(['status' => 200, 'message' => 'Company Updated Successfully', 'data' => $company]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Error Updating company: ' . $e->getMessage(), 'data' => []]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $company = Company::with('companyEmployee.user')->findOrFail($id);

            // Soft delete the company
            $company->delete();

            // Soft delete the associated users
            foreach ($company->companyEmployee as $employee) {
                $employee->user->delete();
            }

            return ok('Company and associated users deleted successfully');
        } catch (ModelNotFoundException $e) {
            return error('Error deleting company and associated users: ' . $e->getMessage());
        }
    }

    /**
     * Search for a name
     */
    public function search(string $name)
    {
        try {
            $companies = Company::where('name', 'like', '%' . $name . '%')->get();
            if ($companies->isEmpty()) {
                return response()->json(
                    [
                        'message' => 'Company not found',
                    ],
                    404,
                );
            }
            return $companies;
        } catch (\Exception $e) {
            return error('Error Finding company: ' . $e->getMessage());
        }
    }
}
