<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'description' => 'Access the system dashboard.'],
            ['name' => 'cases.view', 'display_name' => 'View Cases', 'description' => 'View case records.'],
            ['name' => 'cases.create', 'display_name' => 'Create Cases', 'description' => 'Register new cases.'],
            ['name' => 'cases.update', 'display_name' => 'Update Cases', 'description' => 'Edit case records.'],
            ['name' => 'cases.delete', 'display_name' => 'Delete Cases', 'description' => 'Delete case records.'],
            ['name' => 'participants.manage', 'display_name' => 'Manage Participants', 'description' => 'Manage parties, advocates, witnesses, and court personnel.'],
            ['name' => 'hearings.manage', 'display_name' => 'Manage Hearings', 'description' => 'Schedule and update hearings.'],
            ['name' => 'courtrooms.manage', 'display_name' => 'Manage Courtrooms', 'description' => 'Manage courtroom records.'],
            ['name' => 'events.manage', 'display_name' => 'Manage Court Events', 'description' => 'Manage deadlines, reminders, and court events.'],
            ['name' => 'documents.view', 'display_name' => 'View Documents', 'description' => 'View authorized case documents.'],
            ['name' => 'documents.manage', 'display_name' => 'Manage Documents', 'description' => 'Upload and manage case documents.'],
            ['name' => 'finance.view', 'display_name' => 'View Financial Records', 'description' => 'View fees, payments, bonds, and reports.'],
            ['name' => 'finance.manage', 'display_name' => 'Manage Financial Records', 'description' => 'Record and manage financial transactions.'],
            ['name' => 'users.view', 'display_name' => 'View Users', 'description' => 'View system user accounts.'],
            ['name' => 'users.manage', 'display_name' => 'Manage Users', 'description' => 'Create accounts and assign roles.'],
            ['name' => 'roles.manage', 'display_name' => 'Manage Roles and Permissions', 'description' => 'Manage role assignments and permissions.'],
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'description' => 'Access authorized system reports.'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'description' => $permission['description'],
                ]
            );
        }
    }
}
