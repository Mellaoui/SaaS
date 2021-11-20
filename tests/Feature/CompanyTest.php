<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    private $superAdmin, $admin, $employee, $invitee, $guest, $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'super_admin' => true,
            'name' => 'super admin name'
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
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->company);
        unset($this->guest);
        unset($this->invitee);
        unset($this->employee);
        unset($this->admin);
        unset($this->superAdmin);
    }

    // --- View Any Company --- //

    public function test_super_admin_can_view_any_company()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('companies.index'));

        $response->assertok();
    }

    public function test_guest_can_view_any_company()
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('companies.index'));

        $response->assertok();
    }

    // --- View One View --- //

    public function test_super_admin_can_view_company()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('companies.show', [
            'company' => $this->company
        ]));

        $response->assertOk();
    }

    public function test_admin_can_view_company()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('companies.show', [
            'company' => $this->company
        ]));

        $response->assertOk();
    }

    public function test_employee_can_view_company()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('companies.show', [
            'company' => $this->company
        ]));

        $response->assertOk();
    }

    public function test_guest_cannot_view_company()
    {
        $this->actingAs($this->guest);

        $response = $this->get(route('companies.show', [
            'company' => $this->company
        ]));

        $response->assertSeeText('Only Member');
        $response->assertForbidden();
    }

    // --- Create Company --- //

    public function test_guest_can_create_company()
    {
        $this->actingAs($this->guest);

        $response = $this->post(route('companies.store'), $this->fields());

        $this->assertDatabaseCount('companies', 2);
        $response->assertSeeText('created');
        $response->assertOk();
    }

    // --- Update Company --- //

    public function test_admin_can_update_company()
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('companies.update', [
            'company' => $this->company
        ]), $this->fields());

        $this->company = $this->company->fresh();

        $this->assertEquals(
            $this->fields(),
            $this->company->only('name', 'phone', 'address', 'email')
        );
        $response->assertSeeText('updated');
        $response->assertOk();
    }

    public function test_employee_cannot_update_company()
    {
        $this->actingAs($this->employee);

        $response = $this->patch(route('companies.update', [
            'company' => $this->company
        ]), $this->fields());

        $this->company = $this->company->fresh();

        $this->assertNotEquals(
            $this->fields(),
            $this->company->only('name', 'phone', 'address', 'email')
        );
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    // --- Delete Company --- //

    public function test_super_admin_can_delete_company()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->delete(route('companies.destroy', [
            'company' => $this->company
        ]));

        $this->assertDatabaseCount('companies', 0);
        $response->assertSeeText('deleted');
        $response->assertOk();
    }

    public function test_admin_can_delete_company()
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('companies.destroy', [
            'company' => $this->company
        ]));

        $this->assertDatabaseCount('companies', 0);
        $response->assertSeeText('deleted');
        $response->assertOk();
    }

    public function test_employee_cannot_delete_company()
    {
        $this->actingAs($this->employee);

        $response = $this->delete(route('companies.destroy', [
            'company' => $this->company
        ]));

        $this->assertDatabaseCount('companies', 1);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    // --- <super> Add Employee --- //

    public function test_super_admin_can_add_guest_to_company()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('companies.add', [
            'company' => $this->company,
            'user' => $this->guest
        ]));

        $this->assertDatabaseCount('company_user', 4);
        $response->assertSeeText('successfully added');
        $response->assertOk();
    }

    public function test_admin_cannot_add_guest_to_company()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('companies.add', [
            'company' => $this->company,
            'user' => $this->guest
        ]));

        $this->assertDatabaseCount('company_user', 3);
        $response->assertSeeText('Only Super Admin');
        $response->assertForbidden();
    }

    // public function test_existing_user_cannot_be_added()
    // {
    //     $this->actingAs($this->superAdmin);

    //     $response = $this->get(route('companies.add', [
    //         'company' => $this->company,
    //         'user' => $this->guest
    //     ]));

    //     $this->assertDatabaseCount('company_user', 3);
    //     $response->assertSeeText('already exists');
    //     $response->assertForbidden();
    // }

    // --- Invite User to Company --- //

    public function test_admin_can_invite_guest_to_company()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('companies.invite', [
            'company' => $this->company,
            'user' => $this->guest
        ]));

        $this->assertDatabaseCount('company_user', 4);
        $this->assertTrue($this->company->invitees()->get()->contains('id', $this->guest->id));
        $response->assertSeeText('successfully invited');
        $response->assertOk();
    }

    public function test_employee_cannot_invite_guest_to_company()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('companies.invite', [
            'company' => $this->company,
            'user' => $this->guest
        ]));

        $this->assertDatabaseCount('company_user', 3);
        $response->assertSeeText('Only Admin');
        $response->assertForbidden();
    }

    public function test_existing_user_cannot_be_invited()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('companies.invite', [
            'company' => $this->company,
            'user' => $this->employee
        ]));

        $this->assertDatabaseCount('company_user', 3);
        $response->assertSeeText('already exists');
        $response->assertForbidden();
    }

    // --- Accept Invite Request --- //

    public function test_invitee_can_accept_join_request()
    {
        $this->actingAs($this->invitee);

        $response = $this->get(route('companies.accept', ['company' => $this->company]));

        //assert that the same user is employee.
        $response->assertSeeText('successfully added');
        $response->assertOk();
    }

    public function test_employee_cannot_accept_join_request()
    {
        $this->actingAs($this->employee);

        $response = $this->get(route('companies.accept', ['company' => $this->company]));

        // assert that the same user is employee.
        $response->assertSeeText('Only Invitees');
        $response->assertForbidden();
    }

    // --- Extra --- //

    protected function fields()
    {
        return [
            'name' => 'company name',
            'phone' => 'company phone',
            'address' => 'company address',
            'email' => 'company@mail.com',
        ];
    }
}
