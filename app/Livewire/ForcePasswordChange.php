<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ForcePasswordChange extends Component
{

    public $currentPassword;
    public $password;
    public $password_confirmation;
    public $showCurrentPassword = false;
    public $showNewPassword = false;
    public $showConfirmPassword = false;
    public $isSubmitting = false;

    protected $rules = [
        'currentPassword' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'currentPassword.required' => 'Current password is required.',
        'password.required' => 'New password is required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];
    public function changePassword()
    {
        $this->isSubmitting = true;

        try {
            $this->validate();

            $user = Auth::user();

            // Verify current password
            if (!Hash::check($this->currentPassword, $user->password)) {
                $this->addError('currentPassword', 'The current password is incorrect.');
                $this->isSubmitting = false;
                return;
            }

            // Check if new password is different from current
            if (Hash::check($this->password, $user->password)) {
                $this->addError('password', 'The new password must be different from your current password.');
                $this->isSubmitting = false;
                return;
            }

            // Update password and clear temporary password flags
            $user->update([
                'password' => Hash::make($this->password),
                'temporary_password' => null,
                'is_temporary_password_used' => true,
                'password_changed_at' => now(),
            ]);
           // Refresh the authenticated user
            Auth::setUser($user->fresh());

            // Clear the force password change session
            session()->forget('force_password_change');

            // Use flash()->success() for better user feedback
            flash()->success('Password changed successfully! You can now access the system.');

            // Force a complete page redirect to refresh everything
            $dashboardUrl = $this->getDashboardUrl();

            $this->js("
                setTimeout(() => {
                    window.location.href = '$dashboardUrl';
                }, 1000);
            ");

        } catch (\Exception $e) {
            flash()->error('An error occurred while updating your password. Please try again.');
            $this->addError('general', 'An error occurred while updating your password. Please try again.');
            $this->isSubmitting = false;
        }
    }
    private function getDashboardUrl()
    {
        $user = Auth::user();

        // Get the role through the relationship
        $userRole = $user->role; // This calls the role() method

        // If no role is found, return fallback
        if (!$userRole) {
            return '/';
        }

        // Use role slug for comparison
        $roleSlug = strtolower($userRole->slug);

        switch ($roleSlug) {
            case 'bfo':
                return '/bfo/dashboard';

            case 'pamo':
                return '/pamo/dashboard';

            case 'administrator':
            case 'developer':
                return '/pamo/dashboard';

            default:
                return '/';
        }
    }


    public function render()
    {
        return view('livewire.force-password-change');
    }
}
