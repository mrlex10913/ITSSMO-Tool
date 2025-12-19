<?php

namespace Tests\Feature;

use App\Livewire\Tickets\GuestPortal;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuestPortalDepartmentsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_active_and_guest_visible_departments_are_listed(): void
    {
        $visible = Department::factory()->create(['name' => 'ITSS', 'slug' => 'itss', 'is_active' => true, 'is_guest_visible' => true]);
        Department::factory()->create(['name' => 'Hidden', 'slug' => 'hidden', 'is_active' => true, 'is_guest_visible' => false]);
        Department::factory()->create(['name' => 'Inactive', 'slug' => 'inactive', 'is_active' => false, 'is_guest_visible' => true]);

        Livewire::test(GuestPortal::class)
            ->assertViewHas('departments', function ($deps) use ($visible): bool {
                return $deps->count() === 1 && $deps->first()->slug === $visible->slug;
            });
    }
}
