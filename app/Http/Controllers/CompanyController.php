<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Models\Preferences;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Mail\LoginMail;
use Illuminate\Support\Facades\Mail;
// use App\Http\Helpers\EmployeeNumber;

require_once app_path('Http/Helpers/APIResponse.php');
// require_once app_path('Http/Helpers/EmployeeNumber.php');

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies for super admin.
     *
     * @method GET
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /companies
     * @authentication Requires user authentication
     * @middleware checkRole:admin
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'term' => 'string|min:3|nullable',
                'status' => 'string|nullable',
            ]);

            $query = Company::query();

            if ($request->has('term')) {
                $term = $request->input('term');
                $query->where('name', 'like', '%' . $term . '%');
            }

            if ($request->has('status')) {
                $statusValue = $request->input('status') === 'active' ? 1 : 0;
                $query->where('is_active', $statusValue);
            }

            $companies = $query->get();

            if ($companies->isEmpty()) {
                return ok([]);
            }

            return ok('Companies fetched Successfully: ', $companies);
        } catch (\Exception $e) {
            return error('Error getting companies: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /company/create
     * @authentication Requires user authentication
     * @middleware checkRole:admin
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'website' => 'required|string',
                'cmp_email' => 'required|string|email|unique:companies',
                'location' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'joining_date' => 'required|date',
                'logo' => 'nullable',
            ]);

            // Save the uploaded logo file
            if ($request->file('logo')) {
                $logoPath = $request->file('logo')->store('public/logos');
                // Remove the 'public/' prefix from the path
                $logoPath = str_replace('public/', '', $logoPath);
            }

            $company = Company::create(
                $request->only(['name', 'website', 'cmp_email', 'location']) + [
                    'logo' => $logoPath ?? null,
                ],
            );

            $user = User::create(
                $request->only(['first_name', 'last_name', 'email', 'joining_date']) + [
                    'role' => 'cmp_admin',
                    'password' => bcrypt('password'),
                    'company_id' => $company->id,
                    'emp_number' => generateUniqueEmployeeNumber(),
                ],
            );

            $mailData = [
                'company_name' => $request['name'],
                'email' => $request['email'],
                'password' => 'password',
                'loginLink' => env('LOGIN_URL'),
            ];

            //send mail to the company admin with email and password
            Mail::to($request['email'])->send(new LoginMail($mailData));

            // return created company in the api response
            return ok('Company Created Successfully!!', $company);
        } catch (\Exception $e) {
            return error('Error creating company: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @method GET
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /company/{id}
     * @authentication Requires user authentication
     * @middleware checkRole:admin
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        try {
            $companyData = Company::with('companyAdmin')->findOrFail($id);
            return ok('Company Data Found !!', $companyData);
        } catch (ModelNotFoundException $e) {
            return error('Error Finding company: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /company/update/{id}
     * @authentication Requires user authentication
     * @middleware checkRole:admin
     * @param \Illuminate\Http\Request $request ,string $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        try {
            $company = Company::findOrFail($id);
            $user = $company->companyAdmin;

            $request->validate([
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
            $company->update($request->only(['name', 'website', 'cmp_email', 'location', 'is_active']));

            // Update or create the user (admin) data
            $user->update($request->only(['first_name', 'last_name', 'email', 'joining_date']));

            // return updated company in the api response
            return ok('Company Updated Successfully!!', $company);
        } catch (\Exception $e) {
            return error('Error Finding company: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @method DELETE
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /company/{id}
     * @authentication Requires user authentication
     * @middleware checkRole:admin
     * @param \Illuminate\Http\Request $request, string $id
     * @return \Illuminate\Http\Response
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
            return ok('Company and associated users deleted successfully', 200);
        } catch (\Exception $e) {
            return error('failed to delete the company' . $e->getMessage());
        }
    }

    /**
     * return all the registered companies
     * @method GET
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /registeredCompanies
     * @authentication does not Requires user authentication
     * @return \Illuminate\Http\Response
     */
    public function registeredCompanies()
    {
        try {
            // get company names array for the filtering process
            $companies = Job::with('company')->get()->pluck('company.name');
            $uniqueCompanies = collect($companies)->unique()->values()->all();
            // return company names (unique) in the api response
            return ok('Companies Found !!', $uniqueCompanies);
        } catch (\Exception $e) {
            return error('Error getting companies: ' . $e->getMessage());
        }
    }
}
