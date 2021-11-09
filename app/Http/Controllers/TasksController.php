<?php

namespace App\Http\Controllers;

use App\Exports\TasksExport;
use App\Imports\TasksImport;
use App\Models\Branch;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $this->authorize('create', [Task::class, $branch->company]);

        dd($this->authorize('create', [Task::class, $branch->company]));

        $task = Task::create($this->validateData($request));
        return 'Task ( ' .  $task->title .  ') is added for the branch (' . $branch->title . ').';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }

    public function export()
    {
        return Excel::download(new TasksExport, 'tasks.xlxs');
    }

    public function import()
    {
        Excel::import(new TasksImport,request()->file('file'));
    }

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
            'start_date' => 'sometimes',
            'due_date' => 'sometimes|date',
            'progress' => 'required|date',
            'priority' => 'required',
            'media' => 'sometimes',
            
            'branch_id' => 'required|exists:branches,id',
        ]);
    }

    
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

        return '(' . $user->name . ') was successfully assigned the task (' . $task->title . ').';
    }
}
