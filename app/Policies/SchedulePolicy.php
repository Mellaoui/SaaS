<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class SchedulePolicy
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
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Task $task)
    {
        return $task->branch->company->users()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Users of the company can view its schedules');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Schedule $schedule)
    {
        return $schedule->task->branch->company->users()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Users of the company can view this schedule');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Task $task)
    {
        return $task->branch->company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can create schedules.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Schedule $schedule)
    {
        return $schedule->task->branch->company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can update schedules.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Schedule $schedule)
    {
        return $schedule->task->branch->company->admin()->get()->contains('id', $user->id)
            ? Response::allow()
            : Response::deny('Only Admin can delete schedules.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Schedule $schedule)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Schedule $schedule)
    {
        //
    }
}
