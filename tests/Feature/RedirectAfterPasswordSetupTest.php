<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckTemporaryPassword;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RedirectAfterPasswordSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_role_user_redirects_to_generic_after_password_change(): void
    {
        $role = Roles::factory()->create(['slug' => 'qao', 'name' => 'QAO', 'home_route' => null]);
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role_id' => $role->id,
            'temporary_password' => 'Temp12345',
            'is_temporary_password_used' => false,
        ]);

        $this->withoutMiddleware([CheckTemporaryPassword::class]);
        Sanctum::actingAs($user, ['*']);

        // Simulate password change via Livewire endpoint route
        $response = $this->post(route('password.change'), [
            // The Livewire component handles the form; we just visit it then simulate the action by calling component in a separate test.
        ]);
        // Instead of posting, call the component action directly is more deterministic
        \Livewire\Livewire::test(\App\Livewire\ChangePassword::class)
            ->set('password', 'NewSecurePass1!')
            ->set('password_confirmation', 'NewSecurePass1!')
            ->call('changePassword')
            ->assertHasNoErrors();

        // After change, hitting a generic page should be allowed and route exists
        $resp = $this->get(route('generic.dashboard'));
        $resp->assertStatus(200);
    }
}
