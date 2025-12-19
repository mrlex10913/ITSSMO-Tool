<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\SystemCsatResponse;
use App\Models\User;
use Illuminate\Support\Carbon;

class CsatEnforcement
{
    public const SETTING_KEY = 'csat.enforce_since';

    public static function enforceSince(): ?Carbon
    {
        $val = AppSetting::get(self::SETTING_KEY);
        if (! $val) {
            return null;
        }
        try {
            return $val instanceof Carbon ? $val : Carbon::parse((string) $val);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function isEndUser(User $user): bool
    {
        $slug = strtolower(optional($user->role)->slug ?? '');
        if (! $slug && $user->role_id) {
            $role = \App\Models\Roles::find($user->role_id);
            $slug = $role ? strtolower((string) $role->slug) : '';
        }
        // Consider all roles as end-user except core admin/staff roles
        $adminish = ['itss', 'administrator', 'developer'];
        return ! in_array($slug, $adminish, true);
    }

    public static function requiresOverlay(User $user): bool
    {
        $since = self::enforceSince();
        if (! $since) {
            return false;
        }
        if (! self::isEndUser($user)) {
            return false; // admins/itss/devs excluded
        }
        $latest = SystemCsatResponse::query()
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        if (! $latest) {
            return true;
        }

        return $latest->submitted_at->lt($since);
    }
}
