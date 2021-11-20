<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Task;
use App\Models\User;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param \App\Models\Branch $branch
     * @return \Illuminate\Http\Response
     */
    public function index(Branch $branch)
    {
        $this->authorize('viewAny', [Task::class, $branch]);

        return $branch->tasks->toJson(JSON_PRETTY_PRINT);
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
    public function store(Branch $branch, Request $request)
    {
        $this->authorize('create', [Task::class, $branch]);

        $task = Task::create($this->validateData($request));

        return 'Task ( ' .  $task->title .  ' ) is successfully created for the branch ( ' . $branch->title . ' ).';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch, Task $task)
    {
        $this->authorize('view', $task);

        return $task->toJson(JSON_PRETTY_PRINT);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch, Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Branch  $branch
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Branch $branch, Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($this->validateData($request));

        return 'Task ( ' .  $task->title .  ' ) was successfully updated.';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Branch  $branch
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch, Task $task)
    {
        $this->authorize('delete', $task);

        $taskTitle = $task->title;

        $task->delete();

        return $taskTitle . ' was successfully deleted.';
    }

    // --- Extra Functions --- //

    /**
     *  Assign a task to a user.
     *  @param  \App\Models\Task $task
     *  @param  \App\Models\User $user
     *  @return \Illuminate\Http\Response|string
     */
    public function assignToUser(Task $task, User $user)
    {
        $this->authorize('assignToUser', [$task, $user]);

        $task->users()->save($user);

        return '( ' . $user->name . ' ) was successfully assigned the task ( ' . $task->title . ' ).';
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
            'description' => 'required',
            'start_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'progress' => 'required',
            'priority' => 'required',
            'media' => 'sometimes',

            'branch_id' => 'required|exists:branches,id',
        ]);
    }
}
