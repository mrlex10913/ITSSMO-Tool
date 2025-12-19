<?php

namespace App\Livewire\Dashboard;

use App\Services\Menu\MenuBuilder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.enduser')]
class Generic extends Component
{
    public array $menu = [];

    public function mount(MenuBuilder $builder): void
    {
        $user = Auth::user();
        $this->menu = $user ? $builder->getMenuFor($user) : [];
    }

    public function render()
    {
        return view('livewire.dashboard.generic');
    }
}
