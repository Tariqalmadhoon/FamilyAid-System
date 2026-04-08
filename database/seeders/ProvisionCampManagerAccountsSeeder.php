<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ProvisionCampManagerAccountsSeeder extends Seeder
{
    /**
     * Create or update the super admin and one manager per allowed camp.
     */
    public function run(): void
    {
        $this->call(SyncAllowedCampRegionsSeeder::class);

        $defaultPassword = '12345678';
        $campPermissions = User::configurableCampManagerPermissions();
        $adminRole = Role::findByName('admin', 'web');
        $campManagerRole = Role::findByName('camp_manager', 'web');

        $superAdminNationalId = $this->findAvailableNationalId('990000001');

        $superAdmin = User::firstOrNew(['national_id' => $superAdminNationalId]);
        $superAdmin->fill([
            'name' => 'Super Admin',
            'phone' => '0590000001',
            'password' => Hash::make($defaultPassword),
            'is_staff' => true,
            'region_id' => null,
            'camp_permissions_configured' => false,
        ]);
        $superAdmin->save();

        $superAdmin->syncRoles([$adminRole]);
        $superAdmin->syncPermissions([]);

        $camps = Region::query()
            ->allowedCamps()
            ->orderBy('id')
            ->get();

        foreach ($camps as $index => $camp) {
            $nationalId = $this->findAvailableNationalId(str_pad((string) (910000001 + $index), 9, '0', STR_PAD_LEFT));
            $phone = '059' . str_pad((string) ($index + 1), 7, '0', STR_PAD_LEFT);

            $manager = User::firstOrNew(['national_id' => $nationalId]);
            $manager->fill([
                'name' => 'مدير ' . $camp->name,
                'phone' => $phone,
                'password' => Hash::make($defaultPassword),
                'is_staff' => true,
                'region_id' => $camp->id,
                'camp_permissions_configured' => true,
            ]);
            $manager->save();

            $manager->syncRoles([$campManagerRole]);
            $manager->syncPermissions($campPermissions);
        }

        if ($this->command) {
            $this->command->info('Provisioned super admin and camp manager accounts successfully.');
        }
    }

    private function findAvailableNationalId(string $preferredNationalId): string
    {
        $candidate = (int) $preferredNationalId;

        while (User::query()->where('national_id', (string) $candidate)->exists()) {
            $existingUser = User::query()->where('national_id', (string) $candidate)->first();

            if ($existingUser && $existingUser->is_staff) {
                return (string) $candidate;
            }

            $candidate++;
        }

        return (string) $candidate;
    }
}
