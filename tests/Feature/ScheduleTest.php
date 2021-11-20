<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Schedule;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private $superAdmin, $admin, $employee, $invitee, $guest, $company, $branch, $task, $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'super_admin' => true,
            'name' => 'super_admin'
        ]);
        $this->admin = User::factory()->create(['name' => 'admin name']);
        $this->employee = User::factory()->create(['name' => 'employee name']);
        $this->invitee = User::factory()->create(['name' => 'invitee name']);
        $this->guest = User::factory()->create(['name' => 'guest name']);

        $this->company = Company::factory()
            ->hasAttached($this->admin, ['role' => 'admin'])
            ->hasAttached($this->employee, ['role' => 'employee'])
            ->hasAttached($this->invitee, ['role' => 'invitee'])
            ->create();
        $this->branch = Branch::factory()->for($this->company)->create();
        $this->task = Task::factory()->for($this->branch)->create();
        $this->schedule = Schedule::factory()->for($this->task)->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->schedule);
        unset($this->task);
        unset($this->branch);
        unset($this->company);
        unset($this->superAdmin);
        unset($this->admin);
        unset($this->employee);
        unset($this->user);
    }

    // --- View Any Schedule --- //

    public function test_super_admin_can_view_any_schedule()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('schedules.index', ['task' => $this->task]));

        $response->assertOk();
    }

    public function test_admin_can_view_any_schedule()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('schedules.index', ['task' => $this->task]));

        $response->assertOk();
    }

    public function test_employee_can_view_any_schedule()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('schedules.index', ['task' => $this->task]));

        $response->assertOk();
    }

    public function test_invitee_can_view_any_schedule()
    {
        $this->actingAs($this->invitee);

        $response = $this->get(route('schedules.index', ['task' => $this->task]));

        $response->assertOk();
    }

    public function test_guest_cannot_view_any_schedule()
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('schedules.index', ['task' => $this->task]));

        $response->assertSeeText('Only Users');
        $response->assertForbidden();
    }

    // --- View Schedlue --- //

    public function test_super_admin_can_view_one_schedule()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('schedules.show', [
            'task' => $this->task,
            'schedule' => $this->schedule
        ]));

        $response->assertOk();
    }

    public function test_admin_can_view_one_schedule()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('schedules.show', [
            'task' => $this->task,
            'schedule' => $this->schedule
        ]));

        $response->assertOk();
    }

    public function test_employee_can_view_one_schedule()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('schedules.show', [
            'task' => $this->task,
            'schedule' => $this->schedule
        ]));

        $response->assertOk();
    }

    public function test_invitee_can_view_one_schedule()
    {
        $this->actingAs($this->invitee);

        $response = $this->get(route('schedules.show', [
            'task' => $this->task,
            'schedule' => $this->schedule
        ]));

        $response->assertOk();
    }

    public function test_guest_cannot_view_one_schedule()
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('schedules.show', [
            'task' => $this->task,
            'schedule' => $this->schedule
        ]));

        $response->assertSeeText('Only Users');
        $response->assertForbidden();
    }

    // --- Create Schedule --- //

    public function test_super_admin_can_create_schedule()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post(
            route('schedules.store', ['task' => $this->task]),
            $this->fields()
        );

        $this->assertDatabaseCount('schedules', 2);
        $response->assertSeeText('successfully created');
        $response->assertOk();
    }

    public function test_admin_can_create_schedule()
    {
        $this->actingAs($this->admin);

        $response = $this->post(
            route('schedules.store', ['task' => $this->task]),
            $this->fields()
        );

        $this->assertDatabaseCount('schedules', 2);
        $response->assertSeeText('successfully created');
        $response->assertOk();
    }

    public function test_employee_cannot_create_schedule()
    {
        $this->actingAs($this->employee);

        $response = $this->post(
            route('schedules.store', ['task' => $this->task]),
            $this->fields()
        );

        $this->assertDatabaseCount('schedules', 1);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    public function test_invitee_cannot_create_schedule()
    {
        $this->actingAs($this->invitee);

        $response = $this->post(
            route('schedules.store', ['task' => $this->task]),
            $this->fields()
        );

        $this->assertDatabaseCount('schedules', 1);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    public function test_guest_cannot_create_schedule()
    {
        $this->actingAs($this->guest);

        $response = $this->post(
            route('schedules.store', ['task' => $this->task]),
            $this->fields()
        );

        $this->assertDatabaseCount('schedules', 1);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    // --- Update Schedule --- //

    public function test_super_admin_can_update_schedule()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->patch(
            route('schedules.update', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ]),
            $this->fields()
        );

        $this->schedule = $this->schedule->fresh();

        $this->assertEquals(
            $this->fields(),
            $this->schedule->only([
                'title',
                'day',
                'start_time',
                'close_time',
                'task_id'
            ])
        );
        $response->assertSeeText('successfully updated');
        $response->assertOk();
    }

    public function test_admin_can_update_schedule()
    {
        $this->actingAs($this->admin);

        $response = $this->patch(
            route('schedules.update', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ]),
            $this->fields()
        );

        $this->schedule = $this->schedule->fresh();

        $this->assertEquals(
            $this->fields(),
            $this->schedule->only([
                'title',
                'day',
                'start_time',
                'close_time',
                'task_id'
            ])
        );
        $response->assertSeeText('successfully updated');
        $response->assertOk();
    }

    public function test_employee_cannot_update_schedule()
    {
        $this->actingAs($this->employee);

        $response = $this->patch(
            route('schedules.update', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ]),
            $this->fields()
        );

        $this->schedule = $this->schedule->fresh();

        $this->assertNotEquals(
            $this->fields(),
            $this->schedule->only([
                'title',
                'day',
                'start_time',
                'close_time',
                'task_id'
            ])
        );
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    public function test_invitee_cannot_update_schedule()
    {
        $this->actingAs($this->invitee);

        $response = $this->patch(
            route('schedules.update', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ]),
            $this->fields()
        );

        $this->schedule = $this->schedule->fresh();

        $this->assertNotEquals(
            $this->fields(),
            $this->schedule->only([
                'title',
                'day',
                'start_time',
                'close_time',
                'task_id'
            ])
        );
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    public function test_guest_cannot_update_schedule()
    {
        $this->actingAs($this->guest);

        $response = $this->patch(
            route('schedules.update', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ]),
            $this->fields()
        );

        $this->schedule = $this->schedule->fresh();

        $this->assertNotEquals(
            $this->fields(),
            $this->schedule->only([
                'title',
                'day',
                'start_time',
                'close_time',
                'task_id'
            ])
        );
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    // --- Delete Schedule --- //

    public function test_super_admin_can_delete_schedule()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->delete(
            route('schedules.destroy', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ])
        );

        $this->assertDatabaseCount('schedules', 0);
        $response->assertOk();
    }

    public function test_admin_can_delete_schedule()
    {
        $this->actingAs($this->admin);

        $response = $this->delete(
            route('schedules.destroy', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ])
        );

        $this->assertDatabaseCount('schedules', 0);
        $response->assertOk();
    }

    public function test_employee_cannot_delete_schedule()
    {
        $this->actingAs($this->employee);

        $response = $this->delete(
            route('schedules.destroy', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ])
        );

        $this->assertDatabaseCount('schedules', 1);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    public function test_invitee_cannot_delete_schedule()
    {
        $this->actingAs($this->invitee);

        $response = $this->delete(
            route('schedules.destroy', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ])
        );

        $this->assertDatabaseCount('schedules', 1);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    public function test_guest_cannot_delete_schedule()
    {
        $this->actingAs($this->guest);

        $response = $this->delete(
            route('schedules.destroy', [
                'task' => $this->task,
                'schedule' => $this->schedule
            ])
        );

        $this->assertDatabaseCount('schedules', 1);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    // --- Extra --- //

    protected function fields()
    {
        return [
            'title' => 'schedule title',
            'day' => Carbon::now()->format('Y-M-d H:i'),
            'start_time' => Carbon::parse('09:00')->format('H:i'),
            'close_time' => Carbon::parse('09:00')->addHours(7)->format('H:i'),

            'task_id' => $this->task->id
        ];
    }
}
