<?php

namespace App\Services\Menu;

use App\Models\Menu;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MenuBuilder
{
    /**
     * Get the menu items for a user based on role (and optionally department in future phases).
     *
     * @return array<int, array{label:string,route:string,icon?:string}>
     */
    public function getMenuFor(User $user): array
    {
        // 1) User-specific menus (if any) override role menus
        $userKey = 'menus.user.id:'.$user->id;
        $userMenus = Cache::remember($userKey, now()->addMinutes(10), function () use ($user) {
            return Menu::query()
                ->active()
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get()
                ->map(function (Menu $m) {
                    return [
                        'label' => $m->label,
                        'section' => $m->section,
                        'route' => $m->route,
                        'url' => $m->url,
                        'icon' => $m->icon,
                    ];
                })
                ->values()
                ->all();
        });
        if (! empty($userMenus)) {
            return $userMenus;
        }

        // 2) Fall back to role-based menus
        $role = $this->getRole($user);

        // Try DB-backed menus first
        if ($role) {
            $key = 'menus.role.slug:'.strtolower($role->slug ?? ('id:'.$role->id));
            $menus = Cache::remember($key, now()->addMinutes(10), function () use ($role) {
                return Menu::query()
                    ->active()
                    ->whereHas('roles', function ($q) use ($role) {
                        $q->where('roles.id', $role->id);
                    })
                    ->orderBy('sort_order')
                    ->orderBy('label')
                    ->get()
                    ->map(function (Menu $m) {
                        return [
                            'label' => $m->label,
                            'section' => $m->section,
                            // Prefer route; fall back to url
                            'route' => $m->route,
                            'url' => $m->url,
                            'icon' => $m->icon,
                        ];
                    })
                    ->values()
                    ->all();
            });

            if (! empty($menus)) {
                return $menus;
            }
        }

        // Fallback to config for the role's slug or generic 'user'
        $slug = $this->getRoleSlug($user);
        $config = config('features.roles');
        $roleConfig = $config[$slug] ?? $config['user'] ?? ['menu' => []];

        return $roleConfig['menu'] ?? [];
    }

    /**
     * Get the home route name for a user.
     */
    public function getHomeRouteFor(User $user): string
    {
        // Prefer DB column on role if set
        $role = $this->getRole($user);
        if ($role && $role->home_route) {
            return $role->home_route;
        }

        // Fallback to config mapping
        $slug = $this->getRoleSlug($user);
        $config = config('features.roles');
        $roleConfig = $config[$slug] ?? $config['user'] ?? [];

        return $roleConfig['home'] ?? 'generic.dashboard';
    }

    protected function getRoleSlug(User $user): string
    {
        $role = $this->getRole($user);
        if ($role && $role->slug) {
            return strtolower($role->slug);
        }

        return 'user';
    }

    protected function getRole(User $user): ?Roles
    {
        if ($user->relationLoaded('role') && $user->role) {
            return $user->role;
        }
        if ($user->role_id) {
            return Roles::find($user->role_id);
        }

        return null;
    }

    /**
     * Get menus for a role by slug (used to render department sidebars in layout).
     *
     * @param  string  $slug  e.g. 'pamo', 'bfo', 'itss', 'administrator'
     * @return array<int, array{label:string,route:?string,url:?string,icon:?string}>
     */
    public function getMenuForRoleSlug(string $slug): array
    {
        $slug = strtolower($slug);
        $role = Roles::whereRaw('LOWER(slug) = ?', [$slug])->first();
        if (! $role) {
            return [];
        }

        $key = 'menus.role.slug:'.$slug;

        return Cache::remember($key, now()->addMinutes(10), function () use ($role) {
            return Menu::query()
                ->active()
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('roles.id', $role->id);
                })
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get()
                ->map(function (Menu $m) {
                    return [
                        'label' => $m->label,
                        'section' => $m->section,
                        'route' => $m->route,
                        'url' => $m->url,
                        'icon' => $m->icon,
                    ];
                })
                ->values()
                ->all();
        });
    }

    /** Forget menu cache for a role by slug. */
    public function clearMenuCacheForRoleSlug(string $slug): void
    {
        Cache::forget('menus.role.slug:'.strtolower($slug));
    }

    /** Forget menu cache for a role id. */
    public function clearMenuCacheForRoleId(int $roleId): void
    {
        $role = Roles::find($roleId);
        if ($role && $role->slug) {
            $this->clearMenuCacheForRoleSlug($role->slug);
        }
    }

    /** Forget menu cache for a specific user id. */
    public function clearMenuCacheForUserId(int $userId): void
    {
        Cache::forget('menus.user.id:'.$userId);
    }
}
