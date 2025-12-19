<?php

namespace Tests\Feature;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentMenuVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles exist with correct slugs
        Roles::factory()->create(['name' => 'Administrator', 'slug' => 'administrator']);
        Roles::factory()->create(['name' => 'Developer', 'slug' => 'developer']);
        Roles::factory()->create(['name' => 'PAMO', 'slug' => 'pamo']);
        Roles::factory()->create(['name' => 'BFO', 'slug' => 'bfo']);
    }

    public function test_developer_sees_both_department_links(): void
    {
        $devRole = Roles::query()->where('slug', 'developer')->first();
        $user = User::factory()->create(['role_id' => $devRole->id]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('PAMO')
            ->assertSee('BFO');
    }

    public function test_bfo_user_sees_only_bfo_link(): void
    {
        $bfoRole = Roles::query()->where('slug', 'bfo')->first();
        $user = User::factory()->create(['role_id' => $bfoRole->id]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('BFO')
            ->assertDontSee('PAMO');
    }

    public function test_pamo_user_sees_only_pamo_link(): void
    {
        $pamoRole = Roles::query()->where('slug', 'pamo')->first();
        $user = User::factory()->create(['role_id' => $pamoRole->id]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('PAMO')
            ->assertDontSee('BFO');
    }
}
