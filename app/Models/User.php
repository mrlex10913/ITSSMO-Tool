<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'card_no',
        'id_number',
        'name',
        'email',
        'department',
        'role',
        'password',
        'temporary_password',
        'is_temporary_password_used',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function hasRole($roles): bool
    {
        // Load the role relationship if not already loaded
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        // Get the actual role model through the relationship
        $userRoleModel = $this->getRelation('role');

        if (!$userRoleModel) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        return in_array(strtolower($userRoleModel->slug), array_map('strtolower', $roles));
    }
    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }
     /**
     * Check if the user is a developer
     *
     * @return bool
     */
    public function isDeveloper(): bool
    {
        return $this->hasRole('developer');
    }


}
