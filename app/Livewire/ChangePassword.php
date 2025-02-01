<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public $password;
    public $password_confirmation;
    public $showOverlay = true;
    protected $rules = [
        'password' => 'required|string|min:8|confirmed'
    ];

    public function changePassword(){
        $this->validate();
        $user = Auth::user();
        $user->password = Hash::make($this->password);
        $user->temporary_password = null;
        $user->is_temporary_password_used = true;
        $user->save();

        session()->flash('status', 'Password change successfully');
        return redirect()->route('dashboard');
    }
    public function render()
    {
        return view('livewire.change-password')->layout('layouts.app');
    }
}
