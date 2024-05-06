<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Preferences;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\EmployeeService;

require_once app_path('Http/Helpers/APIResponse.php');

class CompanyEmployeeController extends Controller
{
    /**
     * Register EmployeeService
     */
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of the resource on the basis of search term and filter status if not given return all
     * @method GET
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /employees
     * @authentication Requires user authentication
     * @middleware checkRole:admin,cmp_admin
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'company' => 'string|nullable',
                'status' => 'string|nullable',
            ]);

            $company = Company::where('name', $request->input('company'))->firstOrFail();
            $employees = User::where('company_id', $company->id)->with('company')->get();

            if ($employees->isEmpty() || !$request->input('company')) {
                return ok([]);
            }

            return ok('Employees of ' . $company->name . ' found successfully', $employees);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ok([]);
        } catch (\Exception $e) {
            return error('Error getting employees: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource by companyId
     * @method GET
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /{id}/employees
     * @authentication Requires user authentication
     * @middleware checkRole:admin,cmp_admin
     * @param \Illuminate\Http\Request $request, string $id
     * @return \Illuminate\Http\Response
     */
    public function companyEmployees(string $id, Request $request)
    {
        try {
            $request->validate([
                'term' => 'string|nullable|min:3',
            ]);

            // find the company with the requested id
            $company = Company::findOrFail($id);
            $employees = User::where('company_id', $company->id);

            // filter list on term
            if ($request->has('term')) {
                $term = $request->input('term');
                $employees->where('first_name', 'like', "%$term%");
            }

            $employees = $employees->with('company')->get();

            if ($employees->isEmpty()) {
                return ok([]);
            }

            return ok('Employees of your company found Successfully', $employees);
        } catch (\Exception $e) {
            return error('Error getting employees: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /employee/create
     * @authentication Requires user authentication
     * @middleware checkRole:admin,cmp_admin
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'joining_date' => 'required|date',
                'company_name' => 'required|string',
            ]);

            $company = Company::where('name', $request['company_name'])->first();
            $employeeNumber = $this->employeeService->generateUniqueEmployeeNumber($company->id);
            $employee = User::create(
                $request->only(['first_name', 'last_name', 'email', 'joining_date']) + [
                    'role' => 'employee',
                    'password' => 'password',
                    'company_id' => $company->id,
                    'emp_number' => $employeeNumber,
                    'created_by' => auth()->user()->id,
                ],
            );
            // return newly created employee in the api response
            return ok('Employee Registered Successfully', $employee);
        } catch (\Exception $e) {
            return error('Error Registering Employee: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * @method GET
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /employee/{id}
     * @authentication Requires user authentication
     * @middleware checkRole:admin,cmp_admin
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        try {
            $employee = User::with('company')->findOrFail($id);
            // return found employee in the api response
            return ok('Employee Found !', $employee);
        } catch (ModelNotFoundException $e) {
            return error('Error Finding employee: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /employee/update/{id}
     * @authentication Requires user authentication
     * @middleware checkRole:admin,cmp_admin
     * @param \Illuminate\Http\Request $request, string $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        try {
            $employee = User::findOrFail($id);
            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => ['required', 'string', 'email', Rule::unique('users')->ignore($id)],
                'joining_date' => 'required|date',
            ]);
            $employee->update($request->only(['first_name', 'last_name', 'email', 'joining_date']));
            // return updated employee in the api response
            return ok('Employee Updated Successfully', $employee);
        } catch (\Exception $e) {
            return error('Error Updating Employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @method DELETE
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /employee/{id}
     * @authentication Requires user authentication
     * @middleware checkRole:admin,cmp_admin
     * @param \Illuminate\Http\Request $request, string $id
     * @return \Illuminate\Http\Response
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
