<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ForcePasswordChange extends Component
{
    public $currentPassword;

    public $password;

    public $password_confirmation;

    public $showCurrentPassword = false;

    public $showNewPassword = false;

    public $showConfirmPassword = false;

    public $isSubmitting = false;

    public $visible = true;

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

            // Verify current password: accept either actual password or temporary password
            $matchesActual = Hash::check($this->currentPassword, $user->password);
            $matchesTemp = $user->temporary_password && hash_equals($user->temporary_password, (string) $this->currentPassword);
            if (! ($matchesActual || $matchesTemp)) {
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

            // Update password and clear temporary password flags (query builder to satisfy static analyzer)
            \App\Models\User::where('id', $user->id)->update([
                'password' => Hash::make($this->password),
                'temporary_password' => null,
                'is_temporary_password_used' => true,
                'password_changed_at' => now(),
            ]);

            // Refresh the authenticated user instance
            $user = \App\Models\User::find($user->id);
            Auth::setUser($user);

            // Clear the force password change session
            session()->forget('force_password_change');

            // Use flash()->success() for better user feedback
            flash()->success('Password changed successfully! You can now access the system.');

            // Hide modal immediately
            $this->visible = false;

            // Redirect to appropriate dashboard (server-driven so it works without JS)
            $dashboardUrl = $this->getDashboardUrl();

            return redirect()->to($dashboardUrl);

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
        if (! $userRole) {
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
                return '/';

            case 'itss':
                return '/itss/dashboard';

            default:
                return '/';
        }
    }

    public function render()
    {
        return view('livewire.force-password-change');
    }
}
