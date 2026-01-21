<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Households
            'households.view',
            'households.create',
            'households.update',
            'households.delete',
            'households.verify',
            'households.import',
            'households.export',

            // Members
            'members.view',
            'members.create',
            'members.update',
            'members.delete',

            // Aid Programs
            'programs.view',
            'programs.create',
            'programs.update',
            'programs.delete',

            // Distributions
            'distributions.view',
            'distributions.create',
            'distributions.update',
            'distributions.delete',
            'distributions.export',

            // Reports
            'reports.view',
            'reports.export',

            // Audit Logs
            'audit_logs.view',

            // Users/Staff
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            // Settings
            'settings.view',
            'settings.update',

            // Own household (for citizens)
            'own_household.view',
            'own_household.update',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin - has all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Data Entry - can manage households and members
        $dataEntryRole = Role::create(['name' => 'data_entry']);
        $dataEntryRole->givePermissionTo([
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

        // Auditor - read-only access to everything
        $auditorRole = Role::create(['name' => 'auditor']);
        $auditorRole->givePermissionTo([
            'households.view',
            'members.view',
            'programs.view',
            'distributions.view',
            'reports.view',
            'reports.export',
            'audit_logs.view',
        ]);

        // Distributor - can view and record distributions
        $distributorRole = Role::create(['name' => 'distributor']);
        $distributorRole->givePermissionTo([
            'households.view',
            'members.view',
            'programs.view',
            'distributions.view',
            'distributions.create',
        ]);

        // Citizen - can only manage own household
        $citizenRole = Role::create(['name' => 'citizen']);
        $citizenRole->givePermissionTo([
            'own_household.view',
            'own_household.update',
        ]);
    }
}
