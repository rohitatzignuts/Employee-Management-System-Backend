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

    /**
     * Register EmployeeService
     */
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
            $query = User::with('company')->whereIn('role', ['cmp_admin', 'employee']);

            // filter list on term
            if ($request->has('term')) {
                $query->where('first_name', 'like', '%' . $request->input('term') . '%');
            }

            // filter list on status
            if ($request->has('status')) {
                $query->where('role', $request->input('status'));
            }

            $employees = $query->get();

            // add company_name to the employee object and remove extra company details
            $employees->transform(function ($employee) {
                $employee->company_name = $employee->company->name;
                unset($employee->company);
                return $employee;
            });

            if ($employees->isEmpty()) {
                return ok('No Data For Now !', []);
            }

            return ok('Employees Found!!', $employees);
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
            // find the company with the requested id
            $company = Company::findOrFail($id);

            // filter list on term
            if ($request->has('term')) {
                $employees = User::where('company_id', $company->id)
                    ->where('first_name', 'like', '%' . $request->input('term') . '%')
                    ->get();
            } else {
                $employees = User::where('company_id', $company->id)->get();
            }

            if ($employees->isEmpty()) {
                return ok('No Data For Now !', []);
            }
            return ok('Employee Registered Successfully', $employees, 200);
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
            // generate a new empNumber for the newly created user
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
            return ok('Employee Registered Successfully', $employee, 200);
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
            $employee = User::findOrFail($id);
            $employee->company_name = $employee->company->name;
            $employee->makeHidden('company');
            // return found employee in the api response
            return ok('Employee Found !', $employee, 200);
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
            $cmpEmployee = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => ['required', 'string', 'email', Rule::unique('users')->ignore($id)],
                'joining_date' => 'required|date',
            ]);
            $employee->update($request->only(['first_name', 'last_name', 'email', 'joining_date']) + [
                'updated_by' => auth()->user()->id,
            ]);
            // return updated employee in the api response
            return ok('Employee Updated Successfully', $employee, 200);
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
            return ok('Employee deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return error('Error deleting Employee: ' . $e->getMessage());
        }
    }
}
