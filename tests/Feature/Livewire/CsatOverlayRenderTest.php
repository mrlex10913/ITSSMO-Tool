<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Csat\Overlay;
use Livewire\Livewire;
use Tests\TestCase;

class CsatOverlayRenderTest extends TestCase
{
    public function test_component_renders_without_errors(): void
    {
        Livewire::test(Overlay::class)
            ->assertStatus(200);
    }
}
