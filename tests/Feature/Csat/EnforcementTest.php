<?php

namespace Tests\Feature\Csat;

use App\Models\AppSetting;
use App\Models\Roles;
use App\Models\SystemCsatResponse;
use App\Models\User;
use App\Services\CsatEnforcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_overlay_for_end_user_without_recent_response(): void
    {
        $this->artisan('migrate', ['--no-interaction' => true]);
    $role = Roles::create(['name' => 'BFO', 'slug' => 'bfo']);
    $user = User::factory()->create(['role_id' => $role->id]);

        AppSetting::put(CsatEnforcement::SETTING_KEY, now()->toDateTimeString());

        $this->assertTrue(CsatEnforcement::requiresOverlay($user));
    }

    public function test_does_not_require_for_admin(): void
    {
        $this->artisan('migrate', ['--no-interaction' => true]);
    $role = Roles::create(['name' => 'Administrator', 'slug' => 'administrator']);
    $user = User::factory()->create(['role_id' => $role->id]);
        AppSetting::put(CsatEnforcement::SETTING_KEY, now()->toDateTimeString());
        $this->assertFalse(CsatEnforcement::requiresOverlay($user));
    }

    public function test_recent_response_skips_overlay(): void
    {
        $this->artisan('migrate', ['--no-interaction' => true]);
    $role = Roles::create(['name' => 'PAMO', 'slug' => 'pamo']);
    $user = User::factory()->create(['role_id' => $role->id]);
        AppSetting::put(CsatEnforcement::SETTING_KEY, now()->subDay()->toDateTimeString());

        SystemCsatResponse::create([
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Great',
            'submitted_at' => now(),
        ]);

        $this->assertFalse(CsatEnforcement::requiresOverlay($user));
    }
}
