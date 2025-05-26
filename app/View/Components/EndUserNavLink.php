<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EndUserNavLink extends Component
{
    public $href;
    public $active;
    public $icon;

    public function __construct($href, $active = false, $icon = null)
    {
        $this->href = $href;
        $this->active = $active;
        $this->icon = $icon;
    }

    public function render(): View|Closure|string
    {
        return view('components.end-user-nav-link');
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
