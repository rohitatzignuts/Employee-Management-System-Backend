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

require_once app_path('Http/Helpers/APIResponse.php');

class CompanyController extends Controller
{
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
        $this->authorize('create', Company::class);
        try {
            $companyData =  $request->validate([
                'name' => 'required|string',
                'logo' => 'mimes:jpg,jpeg,png|max:2048',
                'website' => 'required|string',
                'cmp_email' => 'required|string|email|unique:companies',
                'location' => 'required|string',
                'cmp_admin_first_name' => 'required|string',
                'cmp_admin_last_name' => 'required|string',
                'cmp_admin_email' => 'required|email|unique:users,email',
                'cmp_admin_password' => 'required|min:8',
                'emp_number' => 'required|string|unique:company_employees',
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

            $companyEmployee = CompanyEmployee::create([
                'company_id' => $company->id,
                'joining_date' => $companyData['cmp_admin_joining_date'],
                'emp_number' => $companyData['emp_number'],
                'user_id' => $user->id,
            ]);

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
        try{
            $company = Company::findOrFail($id);
            return $company;
        }catch(ModelNotFoundException $e){
            return error('Error Finding company: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update',Company::class);
        try{
            $company = Company::findOrFail($id);
            $companyData =  $request->validate([
                'name' => 'required|string',
                'logo' => 'required|string',
                'website' => 'required|string',
                'cmp_email' => 'required|string|email',
                'location' => 'required|string',
            ]);
            $company->update($companyData);
            return response([
                ok('Company Updated Successfully',$company),
            ]);
        }catch(\Exception $e){
            return error('Error Updating company: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('update',Company::class);
        try{
            $company = Company::findOrFail($id);
            $company->delete();
            return ok('Company Deleted Successfully');
        }catch(ModelNotFoundException $e){
            return error('Error Deleting company: ' . $e->getMessage());
        }
    }

    /**
     * Search for a name
     */
    public function search(string $name)
    {
        try {
            $companies = Company::where('name', 'like', '%'.$name.'%')->get();
            if ($companies->isEmpty()) {
                return response()->json([
                    'message' => 'Company not found',
                ], 404);
            }
            return $companies;
        } catch (\Exception $e) {
            return error('Error Finding company: ' . $e->getMessage());
        }
    }
}
