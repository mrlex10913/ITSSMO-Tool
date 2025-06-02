<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemporaryPasswordController extends Controller
{
    public function markPasswordChanged(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'is_temporary_password_used' => true,
            'temporary_password' => null,
        ]);

        // Clear the session flag
        $request->session()->forget('force_password_change');

        return response()->json(['success' => true]);
    }

}
