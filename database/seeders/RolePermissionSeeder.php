<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = Permission::pluck('id', 'name');

        $assignments = [
            'admin' => array_keys($allPermissions->toArray()),

            'judge' => [
                'dashboard.view',
                'cases.view',
                'cases.update',
                'participants.manage',
                'hearings.manage',
                'courtrooms.manage',
                'events.manage',
                'documents.view',
                'reports.view',
            ],

            'magistrate' => [
                'dashboard.view',
                'cases.view',
                'cases.update',
                'participants.manage',
                'hearings.manage',
                'courtrooms.manage',
                'events.manage',
                'documents.view',
                'reports.view',
            ],

            'advocate' => [
                'dashboard.view',
                'cases.view',
                'documents.view',
            ],

            'clerk' => [
                'dashboard.view',
                'cases.view',
                'cases.create',
                'cases.update',
                'participants.manage',
                'hearings.manage',
                'courtrooms.manage',
                'events.manage',
                'documents.view',
                'documents.manage',
            ],

            'accountant' => [
                'dashboard.view',
                'finance.view',
                'finance.manage',
                'reports.view',
            ],
        ];

        foreach ($assignments as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                continue;
            }

            $permissionIds = [];

            foreach ($permissionNames as $permissionName) {
                if (isset($allPermissions[$permissionName])) {
                    $permissionIds[] = $allPermissions[$permissionName];
                }
            }

            $role->permissions()->sync($permissionIds);
        }
    }
}
