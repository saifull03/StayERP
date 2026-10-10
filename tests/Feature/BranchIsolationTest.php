<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_staff_cannot_switch_to_unauthorized_branch(): void
    {
        $receptionDhaka = User::where('email', 'reception.dhaka@stayinghotel.com')->first();
        $ctgBranch = Branch::where('code', 'CTG')->first();

        // Attempt to switch to CTG branch where user is not assigned
        $response = $this->actingAs($receptionDhaka)
            ->post('/switch-branch', ['branch_id' => $ctgBranch->id]);

        $response->assertSessionHas('error');
        $this->assertNotEquals($ctgBranch->id, session('active_branch_id'));
    }

    public function test_non_super_admin_cannot_switch_to_all_branches(): void
    {
        $managerDhaka = User::where('email', 'manager.dhaka@stayinghotel.com')->first();

        $response = $this->actingAs($managerDhaka)
            ->post('/switch-branch', ['branch_id' => 'all']);

        $response->assertSessionHas('error');
        $this->assertNotEquals('all', session('active_branch_id'));
    }

    public function test_super_admin_can_switch_to_any_branch_or_all(): void
    {
        $admin = User::where('email', 'admin@stayinghotel.com')->first();
        $cxbBranch = Branch::where('code', 'CXB')->first();

        $response = $this->actingAs($admin)
            ->post('/switch-branch', ['branch_id' => $cxbBranch->id]);

        $response->assertSessionHas('success');
        $this->assertEquals($cxbBranch->id, session('active_branch_id'));

        $response2 = $this->actingAs($admin)
            ->post('/switch-branch', ['branch_id' => 'all']);

        $response2->assertSessionHas('success');
        $this->assertEquals('all', session('active_branch_id'));
    }
}
