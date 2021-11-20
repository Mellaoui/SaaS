<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BranchPolicy
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
    public function viewAny(User $user, Company $company)
    {
        return $company->members()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Member of this company can see the branches');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Branch $branch)
    {
        return $branch->company->members()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Member of this company can see the branches');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Company $company)
    {
        return $company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can create company branches.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Branch $branch)
    {
        return $branch->company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can update company branches.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Branch $branch)
    {
        return $branch->company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can delete company branches.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Branch $branch)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Branch $branch)
    {
        //
    }
}
