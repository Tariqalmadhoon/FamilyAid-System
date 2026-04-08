<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'households.view',
            'households.create',
            'households.update',
            'households.delete',
            'households.verify',
            'households.import',
            'households.export',
            'members.view',
            'members.create',
            'members.update',
            'members.delete',
            'programs.view',
            'programs.create',
            'programs.update',
            'programs.delete',
            'distributions.view',
            'distributions.create',
            'distributions.update',
            'distributions.delete',
            'distributions.export',
            'reports.view',
            'reports.export',
            'audit_logs.view',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'settings.view',
            'settings.update',
            'own_household.view',
            'own_household.update',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        Role::findOrCreate('admin', 'web')
            ->syncPermissions(Permission::query()->pluck('name')->all());

        Role::findOrCreate('data_entry', 'web')
            ->syncPermissions([
                'households.view',
                'households.create',
                'households.update',
                'households.import',
                'households.export',
                'members.view',
                'members.create',
                'members.update',
                'members.delete',
                'distributions.view',
                'distributions.create',
                'programs.view',
            ]);

        Role::findOrCreate('auditor', 'web')
            ->syncPermissions([
                'households.view',
                'members.view',
                'programs.view',
                'distributions.view',
                'reports.view',
                'reports.export',
                'audit_logs.view',
            ]);

        Role::findOrCreate('distributor', 'web')
            ->syncPermissions([
                'households.view',
                'members.view',
                'programs.view',
                'distributions.view',
                'distributions.create',
            ]);

        Role::findOrCreate('camp_manager', 'web')
            ->syncPermissions([
                'households.view',
                'households.create',
                'households.update',
                'households.delete',
                'households.verify',
                'households.import',
                'households.export',
                'members.view',
                'members.create',
                'members.update',
                'members.delete',
                'distributions.view',
                'distributions.create',
                'distributions.delete',
                'distributions.export',
            ]);

        Role::findOrCreate('citizen', 'web')
            ->syncPermissions([
                'own_household.view',
                'own_household.update',
            ]);
    }
}