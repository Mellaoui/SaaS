<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Task;
use App\Models\TaskUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private $superAdmin, $admin, $employee, $guest, $company, $branch, $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'super_admin' => true,
            'name' => 'super_admin'
        ]);
        $this->admin = User::factory()->create(['name' => 'admin']);
        $this->employee = User::factory()->create(['name' => 'employee']);
        $this->guest = User::factory()->create(['name' => 'guest']);

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
        unset($this->superAdmin);
        unset($this->admin);
        unset($this->employee);
        unset($this->user);
    }

    // --- View Any Task --- //

    public function test_employees_can_view_any_task()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('tasks.index', [
            'branch' => $this->branch
        ]));

        $response->assertOk();
    }

    public function test_guest_cannot_view_any_task()
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('tasks.index', [
            'branch' => $this->branch
        ]));

        $response->assertSeeText('Only Members');
        $response->assertForbidden();
    }

    // --- View One Task --- //

    public function test_employee_can_view_task()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('tasks.show', [
            'branch' => $this->branch,
            'task' => $this->task
        ]));

        $response->assertOk();
    }

    public function test_guest_cannot_view_task()
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('tasks.show', [
            'branch' => $this->branch,
            'task' => $this->task
        ]));

        $response->assertSeeText('Only Members');
        $response->assertForbidden();
    }

    // --- Create Task --- //

    public function test_super_admin_can_create_task()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post(route('tasks.store', [
            'branch' => $this->branch
        ]), $this->fields());

        $this->assertDatabaseCount('tasks', 2);
        $response->assertSeeText('successfully created');
        $response->assertOk();
    }

    public function test_admin_can_create_task()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('tasks.store', [
            'branch' => $this->branch
        ]), $this->fields());

        $this->assertDatabaseCount('tasks', 2);
        $response->assertSeeText('successfully created');
        $response->assertOk();
    }

    public function test_employee_can_create_task()
    {
        $this->actingAs($this->employee);

        $response = $this->post(route('tasks.store', [
            'branch' => $this->branch
        ]), $this->fields());

        $this->assertDatabaseCount('tasks', 2);
        $response->assertSeeText('successfully created');
        $response->assertOk();
    }

    public function test_guest_cannot_create_task()
    {
        $this->actingAs($this->guest);

        $response = $this->post(route('tasks.store', [
            'branch' => $this->branch
        ]), $this->fields());

        $this->assertDatabaseCount('tasks', 1);
        $response->assertSeeText('Only Members');
        $response->assertForbidden();
    }

    // --- Update Task --- //

    public function test_admin_can_update_task()
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('tasks.update', [
            'branch' => $this->branch,
            'task' => $this->task,
        ]), $this->fields());

        $this->task = $this->task->fresh();

        $this->assertEquals(
            $this->fields(),
            $this->task->only(
                'title',
                'description',
                'start_date',
                'due_date',
                'progress',
                'priority',
                'media',
                'branch_id'
            )
        );
        $response->assertSeeText('successfully updated');
        $response->assertOk();
    }

    public function test_employee_cannot_update_task()
    {
        $this->actingAs($this->employee);

        $response = $this->patch(route('tasks.update', [
            'branch' => $this->branch,
            'task' => $this->task,
        ]), $this->fields());

        $this->task = $this->task->fresh();

        $this->assertNotEquals(
            $this->fields(),
            $this->task->only(
                'title',
                'description',
                'start_date',
                'due_date',
                'progress',
                'priority',
                'media',
                'branch_id'
            )
        );
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    // --- Delete Task --- //

    public function test_admin_can_delete_task()
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('tasks.update', [
            'branch' => $this->branch,
            'task' => $this->task,
        ]));

        $this->assertDatabaseCount('tasks', 0);
        $response->assertSeeText('successfully deleted');
        $response->assertOk();
    }

    public function test_employee_cannot_delete_task()
    {
        $this->actingAs($this->employee);

        $response = $this->delete(route('tasks.update', [
            'branch' => $this->branch,
            'task' => $this->task,
        ]));

        $this->assertDatabaseCount('tasks', 1);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }


    // --- Assign Task to User --- //

    public function test_admin_can_assign_task_to_employee()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('tasks.assign', [
            'user' => $this->employee,
            'task' => $this->task
        ]));

        $this->assertDatabaseCount('task_user', 1);
        $response->assertSeeText('successfully assigned');
        $response->assertOk();
    }

    public function test_employee_cannot_assign_task()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('tasks.assign', [
            'user' => $this->employee,
            'task' => $this->task
        ]));

        $this->assertDatabaseCount('task_user', 0);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    public function test_admin_cannot_assign_task_to_guest()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('tasks.assign', [
            'user' => $this->guest,
            'task' => $this->task
        ]));

        $this->assertDatabaseCount('task_user', 0);
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
            'start_date' => Carbon::now()->format('Y-M-d H:i'),
            'due_date' => Carbon::now()->addDays(8)->format('Y-M-d H:i'),
            'progress' => 'task_progress',
            'priority' => 'task priority',
            'media' => 'task media',

            'branch_id' => $this->branch->id,
        ];
    }
}
