<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\Preferences;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\EmployeeService;
use App\Mail\LoginMail;
use Illuminate\Support\Facades\Mail;

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
        $companies = Company::all()->toArray();
        $companies = array_slice($companies, 1); // Remove the first element (no related user data)

        return $companies;
        // return Company::withTrashed()->get();
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
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'joining_date' => 'required|date',
            ]);

            // Save the uploaded logo file
            $logoPath = $request->file('logo')->store('public/logos');
            // Remove the 'public/' prefix from the path
            $logoPath = str_replace('public/', '', $logoPath);

            $company = Company::create([
                'name' => $companyData['name'],
                'logo' => $logoPath,
                'website' => $companyData['website'],
                'cmp_email' => $companyData['cmp_email'],
                'location' => $companyData['location'],
            ]);

            $employeeNumber = $this->employeeService->generateUniqueEmployeeNumber($company->id);
            $user = User::create([
                'first_name' => $companyData['first_name'],
                'last_name' => $companyData['last_name'],
                'email' => $companyData['email'],
                'role' => 'cmp_admin',
                'password' => bcrypt($companyData['password']),
                'joining_date' => $companyData['joining_date'],
                'company_id' => $company->id,
                'emp_number' => 'EMP' . $employeeNumber,
            ]);

            $preference = Preferences::Create([
                'code' => 'EMP',
                'value' => (int)$employeeNumber,
            ]);

            $mailData = [
                'email' => $companyData['email'],
                'password' => $companyData['password'],
            ];

            Mail::to($companyData['email'])->send(new LoginMail($mailData));
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
            $user = User::where('company_id', $company->id)
                ->latest('id')
                ->first();

            return response()->json([
                'company' => $company,
                'employee' => $user,
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
            $user = User::where('company_id', $company->id)
                ->latest('id')
                ->first();

            $companyData = $request->validate([
                'name' => 'required|string',
                'website' => 'required|string',
                'cmp_email' => ['required', 'string', 'email', Rule::unique('companies')->ignore($id)],
                'location' => 'required|string',
                'is_active' => 'sometimes|boolean',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
                'joining_date' => 'required|date',
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
                'first_name' => $companyData['first_name'],
                'last_name' => $companyData['last_name'],
                'email' => $companyData['email'],
                'joining_date' => $companyData['joining_date'],
            ]);

            return response()->json(['status' => 200, 'message' => 'Company Updated Successfully', 'data' => $company]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Error Updating company: ' . $e->getMessage(), 'data' => []]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        try {
            $company = Company::findOrFail($id);

            // Check if delete type is provided in the request
            $deleteType = $request->input('deleteType');

            // Delete the company and associated users based on the delete type
            if ($deleteType === 'permanent') {
                // Permanently delete the company and associated users
                $company->forceDelete(); // Use forceDelete for permanent deletion
                foreach ($company->users as $user) {
                    $user->forceDelete();
                }
            } else {
                // Soft delete the company and associated users
                $company->delete();
                foreach ($company->users as $user) {
                    $user->delete();
                }
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
