<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Manages system users, settings, and access permissions.',
            ],
            [
                'name' => 'judge',
                'display_name' => 'Judge',
                'description' => 'Manages assigned cases, hearings, and judicial decisions.',
            ],
            [
                'name' => 'magistrate',
                'display_name' => 'Magistrate',
                'description' => 'Handles assigned court cases and proceedings.',
            ],
            [
                'name' => 'advocate',
                'display_name' => 'Advocate',
                'description' => 'Accesses permitted case information for legal representation.',
            ],
            [
                'name' => 'clerk',
                'display_name' => 'Court Clerk',
                'description' => 'Supports case registration, scheduling, and court records.',
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Accountant',
                'description' => 'Manages permitted fees, payments, and financial reports.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                ]
            );
        }
    }
}
