<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAndRoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_super_admin_can_access_super_admin_dashboard(): void
    {
        $admin = User::where('email', 'admin@stayinghotel.com')->first();
        $response = $this->actingAs($admin)->get('/dashboard/super-admin');

        $response->assertStatus(200);
        $response->assertSee('StayingHotel ERP');
        $response->assertSee('Super Admin Organization Overview');
    }

    public function test_branch_manager_can_access_branch_manager_dashboard(): void
    {
        $manager = User::where('email', 'manager.dhaka@stayinghotel.com')->first();
        $response = $this->actingAs($manager)->get('/dashboard/branch-manager');

        $response->assertStatus(200);
        $response->assertSee('Operations Hub');
    }

    public function test_finance_manager_can_access_finance_dashboard(): void
    {
        $finance = User::where('email', 'finance@stayinghotel.com')->first();
        $response = $this->actingAs($finance)->get('/dashboard/finance');

        $response->assertStatus(200);
        $response->assertSee('Finance');
    }

    public function test_cashier_can_access_cashier_dashboard(): void
    {
        $cashier = User::where('email', 'cashier.dhaka@stayinghotel.com')->first();
        $response = $this->actingAs($cashier)->get('/dashboard/cashier');

        $response->assertStatus(200);
        $response->assertSee('Cashier Counter Workspace');
    }

    public function test_receptionist_can_access_receptionist_dashboard(): void
    {
        $reception = User::where('email', 'reception.dhaka@stayinghotel.com')->first();
        $response = $this->actingAs($reception)->get('/dashboard/receptionist');

        $response->assertStatus(200);
        $response->assertSee('Front Desk');
    }

    public function test_housekeeping_can_access_housekeeping_dashboard(): void
    {
        $housekeeping = User::where('email', 'housekeeping.dhaka@stayinghotel.com')->first();
        $response = $this->actingAs($housekeeping)->get('/dashboard/housekeeping');

        $response->assertStatus(200);
        $response->assertSee('Housekeeping Operations');
    }

    public function test_maintenance_can_access_maintenance_dashboard(): void
    {
        $maintenance = User::where('email', 'maintenance.dhaka@stayinghotel.com')->first();
        $response = $this->actingAs($maintenance)->get('/dashboard/maintenance');

        $response->assertStatus(200);
        $response->assertSee('Maintenance Task Requests');
    }

    public function test_auditor_can_access_auditor_dashboard(): void
    {
        $auditor = User::where('email', 'auditor@stayinghotel.com')->first();
        $response = $this->actingAs($auditor)->get('/dashboard/auditor');

        $response->assertStatus(200);
        $response->assertSee('Internal Audit');
    }

    public function test_unauthorized_user_cannot_access_super_admin_dashboard(): void
    {
        $reception = User::where('email', 'reception.dhaka@stayinghotel.com')->first();
        $response = $this->actingAs($reception)->get('/dashboard/super-admin');

        $response->assertStatus(403);
    }
}
