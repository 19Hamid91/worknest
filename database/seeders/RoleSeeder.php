<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Superadmin',
            'Project Manager',
            'User'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $superadmin = User::firstOrCreate(
            ['email' => 'worknest@gmail.com'],
            [
                'username' => 'superadmin',
                'name' => 'Superadmin',
                'password' => bcrypt('123'),
            ]
        );

        if (!$superadmin->hasRole('Superadmin')) {
            $superadmin->assignRole('Superadmin');
        }
    }
}
