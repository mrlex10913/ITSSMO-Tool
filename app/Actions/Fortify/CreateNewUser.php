<?php

namespace App\Actions\Fortify;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'id_number' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'department' => ['nullable'],
            // Accept either role_id (preferred) or role slug (legacy 'role')
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'role' => ['nullable', 'string'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // Resolve role_id: prefer explicit role_id, else try mapping from role slug/name
        $roleId = $input['role_id'] ?? null;
        if (! $roleId && ! empty($input['role'])) {
            $slug = strtolower((string) $input['role']);
            $roleModel = Roles::query()
                ->whereRaw('LOWER(slug) = ?', [$slug])
                ->orWhereRaw('LOWER(name) = ?', [$slug])
                ->first();
            $roleId = $roleModel?->id;
        }

        // Fallback: if still no role resolved, use default role if defined, else 'user' slug
        if (! $roleId) {
            $defaultRole = Roles::where('is_default', true)->first()
                ?: Roles::whereRaw('LOWER(slug) = ?', ['user'])->first();
            $roleId = $defaultRole?->id;
        }

        return User::create([
            'id_number' => $input['id_number'],
            'name' => $input['name'],
            'email' => $input['email'],
            'department' => $input['department'],
            // Do not populate obsolete users.role column; use role_id only
            'role_id' => $roleId,
            'password' => Hash::make($input['password']),
        ]);
    }
}
