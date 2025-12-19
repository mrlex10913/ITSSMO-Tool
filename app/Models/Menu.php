<?php

namespace App\Models;

use App\Services\Menu\MenuBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'section',
        'route',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'menu_role', 'menu_id', 'role_id')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'menu_user', 'menu_id', 'user_id')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function booted(): void
    {
        static::saved(function (Menu $menu): void {
            $menu->clearRelatedRoleMenuCaches();
        });

        static::deleted(function (Menu $menu): void {
            $menu->clearRelatedRoleMenuCaches();
        });
    }

    protected function clearRelatedRoleMenuCaches(): void
    {
        $builder = app(MenuBuilder::class);
        $slugs = $this->roles()->pluck('roles.slug')->unique()->all();
        foreach ($slugs as $slug) {
            $builder->clearMenuCacheForRoleSlug((string) $slug);
        }
        $userIds = $this->users()->pluck('users.id')->unique()->all();
        foreach ($userIds as $uid) {
            $builder->clearMenuCacheForUserId((int) $uid);
        }
    }
}
