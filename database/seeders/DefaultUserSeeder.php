<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the developer role
        $developerRole = Roles::where('slug', 'developer')->first();

        if (!$developerRole) {
            $this->command->error('Developer role not found. Please run the roles migration first.');
            return;
        }

        // Create default developer user
        $user = User::updateOrCreate(
            ['email' => 'alexander.lopez@wnu.sti.edu.ph'],
            [
                'name' => 'Alexander Lopez',
                'email' => 'alexander.lopez@wnu.sti.edu.ph',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), // Change this to a secure password
                'role_id' => $developerRole->getAttribute('id'),
                'id_number' => '20029-0003',
                'department' => 'ITSS',
                'role' => 'developer', // Legacy role field if still used
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Default developer user created successfully:');
        $this->command->info('Email: alexander.lopez@wnu.sti.edu.ph');
        $this->command->info('Password: password123');
        $this->command->info('Role: Developer');
        $this->command->warn('Please change the default password after first login!');
    }
}
