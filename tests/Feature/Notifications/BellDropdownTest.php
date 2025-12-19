<?php

namespace Tests\Feature\Notifications;

use App\Livewire\Notifications\BellDropdown;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BellDropdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_bell_dropdown_renders_for_authenticated_user(): void
    {
        $this->artisan('migrate', ['--no-interaction' => true]);
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(BellDropdown::class)
            ->assertStatus(200)
            ->assertSee('Notifications')
            ->assertSee('No recent notifications');
    }
}
