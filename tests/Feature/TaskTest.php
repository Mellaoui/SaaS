<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Task;
use App\Models\TaskUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private $admin, $employee, $guest, $company, $branch, $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['name' => 'Admin']);
        $this->employee = User::factory()->create(['name' => 'Employee']);
        $this->guest = User::factory()->create(['name' => 'Guest']);

        $this->company = Company::factory()
            ->hasAttached($this->admin, ['role' => 'admin'])
            ->hasAttached($this->employee, ['role' => 'employee'])
            ->create();
        $this->branch = Branch::factory()->for($this->company)->create();
        $this->task = Task::factory()->for($this->branch)->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->task);
        unset($this->branch);
        unset($this->company);
        unset($this->admin);
        unset($this->employee);
        unset($this->user);
    }

    public function test_admin_can_create_task()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->admin);

        $response = $this->post(route('tasks.store', [
            'branch' => $this->branch
        ]), $this->fields());

        $this->assertCount(2, Task::all());
        $response->assertSeeText('added');
        $response->assertOk();
    }

    public function test_employee_can_create_task()
    {
        $this->actingAs($this->employee);

        $response = $this->post(route('tasks.store', [
            'branch' => $this->branch
        ]), $this->fields());

        $this->assertCount(2, Task::all());
        $response->assertSeeText('added');
        $response->assertOk();
    }

    public function test_guest_cannot_create_task() {
        $this->actingAs($this->guest);

        $response = $this->post(route('tasks.store', [
            'branch' => $this->branch
        ]), $this->fields());

        $this->assertCount(1, Task::all());
        $response->assertSeeText('add');
        $response->assertForbidden();
    }

    public function test_admin_can_assign_task_to_employee()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->admin);

        $response = $this->get(route('tasks.assign', [
            'user' => $this->employee,
            'task' => $this->task
        ]));

        $this->assertCount(1, TaskUser::all());
        $response->assertSeeText('successfully');
        $response->assertOk();
    }

    public function test_employee_cannot_assign_task()
    {
        $this->actingAs($this->employee);

    //     // next
    // }
        $response = $this->get(route('tasks.assign', [
            'user' => $this->employee,
            'task' => $this->task
        ]));

        $this->assertCount(0, TaskUser::all());
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    public function test_non_employee_cannot_be_assigned_task()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('tasks.assign', [
            'user' => $this->guest,
            'task' => $this->task
        ]));

        $this->assertCount(0, TaskUser::all());
        $response->assertSeeText('Only Employees');
        $response->assertForbidden();
    }


    /**
     * Returns the required fields to create a task
     *
     * @param void
     * @return array
     */
    protected function fields()
    {
        return [
            'title' => 'task title',
            'description' => 'task description',
            'start_date' => 'task start_date',
            'due_date' => 'task due_date',
            'progress' => 'task_progress',
            'priority' => 'task priority',
            'media' => 'task media',

            'branch_id' => $this->branch->id,
        ];
    }
}
