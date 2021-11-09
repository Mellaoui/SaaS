<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Company $company)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Company $company)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Company $company, Request $request)
    {
        $this->authorize('create', [Branch::class, $company]);

        $branch = Branch::create($this->validateData($request));

        return 'Branch . ( ' . $branch->title . ' ) is successfully created for ( ' . $branch->company->name . ' ).';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company, Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company, Branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company, Branch $branch)
    {
        $this->authorize('update', $branch);

        $branch->update($this->validateData($request));

        return 'Branch ( ' . $branch->title . ' ) was successfully updated.';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company, Branch $branch)
    {
        $this->authorize('delete', $branch);

        $branchTitle = $branch->title;

        $branch->delete();

        return 'Branch ( ' . $branchTitle . ' ) was successfully deleted.';
    }

    // --- Validation --- //

    /**
     * Validate the request
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function validateData($request)
    {
        return $request->validate([
            'title' => 'required',
            'status' => 'required',
            'address' => 'required',
            'phone' => 'required',

            'company_id' => 'required|exists:companies,id'
        ]);
    }
}