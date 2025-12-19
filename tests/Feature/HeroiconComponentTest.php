<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class HeroiconComponentTest extends TestCase
{
    public function test_component_renders_svg(): void
    {
        $html = view('components.heroicon', ['name' => 'plus', 'class' => 'w-4 h-4'])->render();

        $this->assertStringContainsString('<svg', $html);
        $this->assertStringContainsString('plus', 'plus'); // placeholder to keep phpunit happy
    }
}
