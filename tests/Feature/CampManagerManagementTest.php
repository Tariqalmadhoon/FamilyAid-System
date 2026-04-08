<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CampManagerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_create_camp_manager_from_superadmin_panel(): void
    {
        $admin = $this->createAdmin();
        [$campA] = $this->createCamps();

        $response = $this->actingAs($admin)->post(route('admin.camp-managers.store'), [
            'name' => 'Camp Manager A',
            'national_id' => '900000111',
            'phone' => '0590000111',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'region_id' => $campA->id,
            'permissions' => [
                'households.view',
                'households.update',
                'distributions.view',
            ],
        ]);

        $response->assertRedirect(route('admin.camp-managers.index'));

        $this->assertDatabaseHas('users', [
            'national_id' => '900000111',
            'region_id' => $campA->id,
            'is_staff' => true,
            'camp_permissions_configured' => true,
        ]);

        $manager = User::where('national_id', '900000111')->firstOrFail();

        $this->assertTrue($manager->hasRole('camp_manager'));
        $this->assertTrue($manager->hasDirectPermission('households.view'));
        $this->assertTrue($manager->hasDirectPermission('households.update'));
        $this->assertTrue($manager->hasDirectPermission('distributions.view'));
        $this->assertFalse($manager->hasDirectPermission('households.verify'));
    }

    public function test_configured_camp_manager_cannot_verify_without_explicit_permission(): void
    {
        [$campA] = $this->createCamps();
        $manager = $this->createCampManager($campA, ['households.view']);
        $household = $this->createHousehold($campA, 'Pending Household', '123450001', 'pending');

        $this->actingAs($manager)
            ->post(route('admin.households.verify', $household))
            ->assertForbidden();

        $this->assertDatabaseHas('households', [
            'id' => $household->id,
            'status' => 'pending',
        ]);
    }

    public function test_configured_camp_manager_cannot_open_distribution_create_without_permission(): void
    {
        [$campA] = $this->createCamps();
        $manager = $this->createCampManager($campA, ['households.view', 'distributions.view']);

        $this->actingAs($manager)
            ->get(route('admin.distributions.create'))
            ->assertForbidden();
    }

    public function test_household_export_response_has_csv_filename_and_content_type(): void
    {
        $admin = $this->createAdmin();
        [$campA] = $this->createCamps();
        $this->createHousehold($campA, 'Export Test', '123450009', 'verified');

        $response = $this->actingAs($admin)
            ->get(route('admin.import-export.export-households'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('.csv', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('attachment;', (string) $response->headers->get('content-disposition'));
    }

    /**
     * @return array{0: \App\Models\Region, 1: \App\Models\Region}
     */
    private function createCamps(): array
    {
        $parent = Region::create([
            'name' => 'Main Region',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        $campA = Region::create([
            'name' => Region::ALLOWED_CAMP_REGION_NAMES[0],
            'code' => 'CAMP-A',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        $campB = Region::create([
            'name' => Region::ALLOWED_CAMP_REGION_NAMES[1],
            'code' => 'CAMP-B',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        return [$campA, $campB];
    }

    private function createAdmin(): User
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'national_id' => '900000001',
            'phone' => '0590000001',
            'password' => Hash::make('password'),
            'is_staff' => true,
        ]);

        $admin->assignRole('admin');

        return $admin;
    }

    private function createCampManager(Region $camp, array $permissions): User
    {
        $manager = User::create([
            'name' => 'Configured Camp Manager',
            'national_id' => '900000222',
            'phone' => '0590000222',
            'password' => Hash::make('password'),
            'is_staff' => true,
            'region_id' => $camp->id,
            'camp_permissions_configured' => true,
        ]);

        $manager->assignRole('camp_manager');
        $manager->syncPermissions($permissions);

        return $manager;
    }

    private function createHousehold(Region $camp, string $headName, string $nationalId, string $status = 'verified'): Household
    {
        return Household::create([
            'head_national_id' => $nationalId,
            'head_name' => $headName,
            'region_id' => $camp->id,
            'status' => $status,
            'primary_phone' => '0501234567',
        ]);
    }
}
