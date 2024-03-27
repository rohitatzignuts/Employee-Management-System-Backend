<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;

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
        $this->authorize('create',Company::class);
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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::find($id);
        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update',Company::class);
        $company = Company::find($id);
        $company->update($request->all());
        return $company;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('update',Company::class);
        $company = Company::find($id);
        $company->delete();
        return response()->json([
            'message' => 'Company Deleted Successfully',
        ]);
    }
    /**
     * Search for a name
     */
    public function search(string $name)
    {
        return Company::where('name', 'like', '%'.$name.'%')->get();
    }
}
