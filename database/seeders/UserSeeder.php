<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('role_name', 'Owner')
            ->firstOrFail();

        $email = config('seeding.admin_email');
        $password = config('seeding.admin_password');

        if (!$email || !$password) {
            throw new RuntimeException(
                'Administrator credentials are missing.'
            );
        }

        $user = User::firstOrNew([
            'email' => $email,
        ]);

        if (!$user->exists) {
            $user->name = 'System Owner';
            $user->password = $password;
            $user->role_id = $role->role_id;
            $user->is_active = true;
            $user->email_verified_at = now();

            $user->save();
        }
    }
}
