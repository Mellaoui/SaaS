<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Company $company)
    {
        return $company->members()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Member can view the company.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Company $company)
    {
        return $company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can update this company');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Company $company)
    {
        return $company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can delete this company');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Company $company)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Company $company)
    {
        //
    }

    /**
     * Adds the user as invitee of the company.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @param  \App\Models\User  $guest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function inviteUser(User $user, Company $company, User $guest)
    {
        return $company->admin()->get()->contains('id', $user->id)
            ? ($company->users()->get()->contains('id', $guest->id)
                ? Response::deny('User already exists in the company')
                : Response::allow())
            : Response::deny('Only Admin can invite users to join the company');
    }

    /**
     * Adds the user as invitee of the company.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function acceptInvite(User $user, Company $company)
    {
        return $company->invitees()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Invitees can accept invites');
    }

    /**
     * Adds the user as employee of the company.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @param  \App\Models\User  $guest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function addEmployee(User $user, Company $company, User $guest)
    {
        return Response::deny('Only Super Admin can directly add employees without a previous invitation');
    }
}
