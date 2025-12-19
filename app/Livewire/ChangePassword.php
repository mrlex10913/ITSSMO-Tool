<?php

namespace App\Livewire;

use App\Services\Menu\MenuBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
// Livewire redirects are triggered via helper methods and don't return values
use Livewire\Component;

class ChangePassword extends Component
{
    public $password;

    public $password_confirmation;

    public $showOverlay = true;

    protected $rules = [
        'password' => 'required|string|min:8|confirmed',
    ];

    public function changePassword(): void
    {
        $this->validate();
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->password = Hash::make($this->password);
        $user->temporary_password = null;
        $user->is_temporary_password_used = true;
        $user->save();

        session()->flash('status', 'Password change successfully');
        // Redirect based on role/home route, falling back to generic dashboard for unknown roles
        try {
            /** @var MenuBuilder $builder */
            $builder = app(MenuBuilder::class);
            $routeName = $builder->getHomeRouteFor($user);
            $this->redirectRoute($routeName, navigate: true);
        } catch (\Throwable $e) {
            $this->redirectRoute('generic.dashboard', navigate: true);
        }
    }

    public function render(): View
    {
        return view('livewire.change-password');
    }
}
