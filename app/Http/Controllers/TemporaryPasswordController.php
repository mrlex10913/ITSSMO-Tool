<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TemporaryPasswordController extends Controller
{
    public function updatePassword(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => ['required', 'string', 'confirmed', 'min:8'],
            ]);

            $user = Auth::user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'errors' => [
                        'current_password' => ['The current password is incorrect.']
                    ]
                ], 422);
            }

            // Check if new password is different from current
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'errors' => [
                        'password' => ['The new password must be different from your current password.']
                    ]
                ], 422);
            }

            // Update password and clear temporary password flags
            $user->update([
                'password' => Hash::make($request->password),
                'is_temporary_password_used' => true,
                'temporary_password' => null,
                'password_changed_at' => now(), // Optional: track when password was changed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Password update error: ' . $e->getMessage());
            return response()->json([
                'errors' => [
                    'general' => ['An error occurred while updating your password. Please try again.']
                ]
            ], 500);
        }
    }

    public function markPasswordChanged()
    {
        try {
            Auth::user()->update([
                'is_temporary_password_used' => true,
                'temporary_password' => null,
                'password_changed_at' => now()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update password status'], 500);
        }
    }

}
