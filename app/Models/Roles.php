<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'is_default', 'home_route'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_role', 'role_id', 'menu_id')->withTimestamps();
    }

    public function getHomeRoute(): string
    {
        return $this->home_route ?: 'generic.dashboard';
    }
}
