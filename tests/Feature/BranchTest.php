<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BranchTest extends TestCase
{
    use RefreshDatabase;

    private $superAdmin, $admin, $company, $guest, $branch, $employee;

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
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->branch);
        unset($this->company);
        unset($this->guest);
        unset($this->employee);
        unset($this->admin);
        unset($this->superAdmin);
    }

    // --- View Any Branch --- //

    public function test_super_admin_can_view_any_branch()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('branches.index', [
            'company' => $this->company
        ]));

        $response->assertOk();
    }

    public function test_admin_can_view_any_branch()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('branches.index', [
            'company' => $this->company
        ]));

        $response->assertOk();
    }

    public function test_employee_can_view_any_branch()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('branches.index', [
            'company' => $this->company
        ]));

        $response->assertOk();
    }

    public function test_guest_cannot_view_any_branch()
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('branches.index', [
            'company' => $this->company
        ]));

        $response->assertSeeText('Only Member');
        $response->assertForbidden();
    }

    // --- View One Branch --- //

    public function test_super_admin_can_view_branch()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('branches.show', [
            'company' => $this->company,
            'branch' => $this->branch
        ]));

        $response->assertOk();
    }

    public function test_admin_can_view_branch()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('branches.show', [
            'company' => $this->company,
            'branch' => $this->branch
        ]));

        $response->assertOk();
    }

    public function test_employee_can_view_branch()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('branches.show', [
            'company' => $this->company,
            'branch' => $this->branch
        ]));

        $response->assertOk();
    }

    public function test_guest_cannot_view_branch()
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('branches.show', [
            'company' => $this->company,
            'branch' => $this->branch
        ]));

        $response->assertSeeText('Only Member');
        $response->assertForbidden();
    }

    // --- Create Branch --- //

    public function test_super_admin_can_create_branch()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post(route('branches.store', [
            'company' => $this->company
        ]), $this->fields());

        $this->assertCount(2, Branch::all());
        $response->assertSeeText('successfully created');
        $response->assertOk();
    }

    public function test_admin_can_create_branch()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('branches.store', [
            'company' => $this->company
        ]), $this->fields());

        $this->assertCount(2, Branch::all());
        $response->assertSeeText('successfully created');
        $response->assertOk();
    }

    public function test_employee_cannot_create_branch()
    {
        $this->actingAs($this->employee);

        $response = $this->post(route('branches.store', [
            'company' => $this->company
        ]), $this->fields());

        $this->assertCount(1, Branch::all());
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    // --- Update Branch --- //

    public function test_super_admin_can_update_branch()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->patch(route('branches.update', [
            'company' => $this->company,
            'branch' => $this->branch,
        ]), $this->fields());

        $this->branch = $this->branch->fresh();

        $this->assertEquals(
            $this->fields(),
            $this->branch->only('title', 'status', 'address', 'phone', 'company_id')
        );
        $response->assertSeeText('successfully updated');
        $response->assertOk();
    }

    public function test_admin_can_update_branch()
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('branches.update', [
            'company' => $this->company,
            'branch' => $this->branch,
        ]), $this->fields());

        $this->branch = $this->branch->fresh();

        $this->assertEquals(
            $this->fields(),
            $this->branch->only('title', 'status', 'address', 'phone', 'company_id')
        );
        $response->assertSeeText('successfully updated');
        $response->assertOk();
    }

    public function test_employee_cannot_update_branch()
    {
        $this->actingAs($this->employee);

        $response = $this->patch(route('branches.update', [
            'company' => $this->company,
            'branch' => $this->branch,
        ]), $this->fields());

        $this->branch = $this->branch->fresh();

        $this->assertNotEquals(
            $this->fields(),
            $this->branch->only('title', 'status', 'address', 'phone', 'company_id')
        );
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    // --- Delete Branch --- //

    public function test_super_admin_can_delete_branch()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->delete(route('branches.destroy', [
            'company' => $this->company,
            'branch' => $this->branch
        ]));

        $this->assertCount(0, Branch::all());
        $response->assertSeeText('successfully deleted');
        $response->assertOk();
    }

    public function test_admin_can_delete_branch()
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('branches.destroy', [
            'company' => $this->company,
            'branch' => $this->branch
        ]));

        $this->assertCount(0, Branch::all());
        $response->assertSeeText('successfully deleted');
        $response->assertOk();
    }

    public function test_employee_cannot_delete_branch()
    {
        $this->actingAs($this->employee);

        $response = $this->delete(route('branches.destroy', [
            'company' => $this->company,
            'branch' => $this->branch
        ]));

        $this->assertCount(1, Branch::all());
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    // --- Extra --- //

    protected function fields()
    {
        return [
            'title' => 'branch title',
            'status' => 'branch status',
            'address' => 'branch address',
            'phone' => 'branch phone',

            'company_id' => $this->company->id
        ];
    }
}
