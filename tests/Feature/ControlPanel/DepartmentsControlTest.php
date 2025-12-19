<?php

namespace Tests\Feature\ControlPanel;

use App\Livewire\ControlPanel\DepartmentsControl;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DepartmentsControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = Roles::firstOrCreate(['slug' => 'administrator'], ['name' => 'Administrator']);
        /** @var User $user */
        $user = User::factory()->createOne(['role_id' => $admin->id]);
        $this->actingAs($user);
    }

    public function test_renders_and_validates(): void
    {
        Livewire::test(DepartmentsControl::class)
            ->assertStatus(200)
            ->call('create')
            ->assertHasErrors(['name' => 'required', 'slug' => 'required']);
    }
}
