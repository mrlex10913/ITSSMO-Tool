<?php

namespace Tests\Feature\Routes;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenericHelpdeskRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_generic_helpdesk_route_exists_for_authenticated_user(): void
    {
        Roles::firstOrCreate(['slug' => 'administrator'], ['name' => 'Administrator']);
        /** @var User $user */
        $user = User::factory()->createOne([
            'role_id' => (int) Roles::where('slug', 'administrator')->first()->id,
        ]);
        $this->actingAs($user);

        $resp = $this->get(route('generic.helpdesk'));
        $resp->assertStatus(200);
    }
}
