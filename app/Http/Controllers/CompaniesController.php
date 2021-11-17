<?php
namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Company::class);

        return Company::all()->toJson(JSON_PRETTY_PRINT);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Company::class);

        $company = Company::create($this->validateData($request));

        return 'Company ( ' . $company->name . ' ) was successfully created by ' . Auth::user()->name . '.';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        $this->authorize('view', $company);

        return $company->toJson(JSON_PRETTY_PRINT);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $this->authorize('update', $company);

        $company->update($this->validateData($request));

        return 'Company ( ' . $company->name . ' ) was successfully updated.';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);

        $companyName = $company->name;

        $company->delete();

        return 'Company ( ' . $companyName . ' ) was successfully deleted';
    }

    // --- Extra --- //

    public function inviteUser(Company $company, User $user)
    {
        $this->authorize('inviteUser', [$company, $user]);

        $company->invitees()->save($user, ['role' => 'invitee']);

        return $user->name . ' was successfully invited to join ( ' . $company->name . ' ).';
    }

    public function acceptInvite(Company $company)
    {
        $user = Auth::user();

        $this->authorize('acceptInvite', $company);

        $company->invitees()->updateExistingPivot($user->id, ['role' => 'employee']);

        return $user->name . ' was successfully added to ( ' . $company->name . ' ) as an Employee.';
    }

    public function addEmployee(Company $company, User $user)
    {
        $this->authorize('addEmployee', [$company, $user]);

        $company->employees()->save($user, ['role' => 'employee']);

        return $user->name . ' was successfully added to ( ' . $company->name . ' ) as an Employee.';
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
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required|email',
        ]);
    }
}