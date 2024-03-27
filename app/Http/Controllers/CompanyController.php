<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                'logo' => 'required|string',
                'website' => 'required|string',
                'email' => 'required|string|email|unique:companies',
            ]);
            $company = Company::create($companyData);
            return response()->json([
                'message' => 'Company Created Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating company: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $company = Company::findOrFail($id);
            return response()->json($company);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Company found' . $e->getMessage(),
            ]);
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
            $company->update($request->all());
            return $company;
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Error updating company: ' . $e->getMessage(),
            ], 500);
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
            return response()->json([
                'message' => 'Company Deleted Successfully',
            ]);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Company not found' . $e->getMessage(),
            ]);
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
            return response()->json([
                'message' => 'An error occurred while searching for companies.',
            ], 500);
        }
    }
}
