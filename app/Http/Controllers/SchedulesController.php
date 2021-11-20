<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Schedule;
use App\Models\Task;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Task $task
     * @return json
     */
    public function index(Task $task)
    {
        $this->authorize('viewAny', [Schedule::class, $task]);

        $companySchedules = collect([]);

        foreach ($task->branch->tasks as $v) {
            $companySchedules->push($v->schedule);
        }

        return $companySchedules->toJson(JSON_PRETTY_PRINT);
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
     * @param  \Illuminate\Http\Task  $task
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Task $task, Request $request)
    {
        $this->authorize('create', [Schedule::class, $task]);

        $schedule = Schedule::create($this->validateData($request));

        return 'The Schedule ' . $schedule->title . ' was successfully created.';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @param  \App\Models\Schedule  $schedule
     * @return json
     */
    public function show(Task $task, Schedule $schedule)
    {
        $this->authorize('view', $schedule);

        return $schedule->toJson(JSON_PRETTY_PRINT);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task, Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Task  $task
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return string
     */
    public function update(Task $task, Request $request, Schedule $schedule)
    {
        $this->authorize('update', $schedule);

        $schedule->update($this->validateData($request));

        return 'The Schedule ' . $schedule->title . ' was successfully updated.';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task, Schedule $schedule)
    {
        $this->authorize('delete', $schedule);

        $scheduleTitle = $schedule->title;

        $schedule->delete();

        return 'The schedule ' . $scheduleTitle . ' was successfully deleted';
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
            'day' => 'required',
            'start_time' => 'required',
            'close_time' => 'required',

            'task_id' => 'required|exists:tasks,id',
        ]);
    }
}
