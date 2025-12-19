<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckTemporaryPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GenericDashboardUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_generic_dashboard_shows_under_development_announcement(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->withoutMiddleware([CheckTemporaryPassword::class]);
        Sanctum::actingAs($user, ['*']);

        $resp = $this->get(route('generic.dashboard'));
        $resp->assertOk();
        $resp->assertSee('This page is still under development');
        $resp->assertSee('Announcements');
        $resp->assertSee('News');
        $resp->assertSee('Quick Links');
    }
}
