<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TaskPolicy
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
     * @param  \App\Models\User  $user
     * @param \App\Models\Branch $branch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Branch $branch)
    {
        $company = $branch->company;

        return $company->members()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Members of ( ' . $company->name . ' ) company can view tasks');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Task $task)
    {
        $company = $task->branch->company;

        return $company->members()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Members of ( ' . $company->name . ' ) company can view this task');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Branch $branch)
    {
        $company = $branch->company;

        return $company->members()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Members of ( ' . $company->name . ' ) company can create tasks');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Task $task)
    {
        return $task->branch->company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can update tasks.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Task $task)
    {
        return $task->branch->company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can delete tasks.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Task $task)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Task $task)
    {
        //
    }

    /**
     * Determine whether the user can assign a task to another user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @param  \App\Models\User  $employee
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assignToUser(User $user, Task $task, User $employee)
    {
        $company = $task->branch->company;

        return $company->admin()->get()->contains('id', $user->id)
            ? ($company->employees()->get()->contains('id', $employee->id)
                ? Response::allow()
                : Response::deny('Only Employees of (' . $company->name . ') can be assigned this task'))
            : Response::deny('Only Admin of (' . $company->name . ') can assign tasks');
    }
}
