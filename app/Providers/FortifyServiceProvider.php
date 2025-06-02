<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                // Get authenticated user with role relationship
                $user = auth()->user();

                // Get role directly from database
                if (!$user->role_id) {
                    abort(403, 'No role assigned to user');
                }

                $roleRecord = Roles::find($user->role_id);

                if (!$roleRecord) {
                    abort(403, 'Invalid role assigned to user');
                }

                // Get role slug for comparison
                $roleSlug = strtolower($roleRecord->slug);

                // Redirect based on role slug
                switch ($roleSlug) {
                    case 'administrator':
                    case 'developer':
                        try {
                            return redirect()->route('dashboard');
                        } catch (\Exception $e) {
                            return redirect('/');
                        }

                    case 'pamo':
                        try {
                            return redirect()->route('pamo.dashboard');
                        } catch (\Exception $e) {
                            return redirect('/pamo/dashboard');
                        }

                    case 'bfo':
                        try {
                            return redirect()->route('bfo.dashboard');
                        } catch (\Exception $e) {
                            return redirect('/bfo/dashboard');
                        }

                    case 'user':
                        try {
                            return redirect()->route('dashboard');
                        } catch (\Exception $e) {
                            return redirect('/');
                        }

                    default:
                        abort(403, 'Unauthorized role: ' . $roleRecord->name);
                }
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function (Request $request){
            $user = User::where('email', $request->email)->first();

            if($user && Hash::check($request->password, $user->password)){
                if($user->temporary_password === $request->password){
                    return $user;
                }

                return $user;
            }

            return null;
        });
    }
}
