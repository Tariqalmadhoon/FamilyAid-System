<?php

namespace Tests\Feature;

use App\Exports\HouseholdsExport;
use App\Imports\HouseholdsImport;
use App\Models\Household;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class CampManagerAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_camp_manager_only_sees_households_from_their_camp(): void
    {
        [$campA, $campB] = $this->createCamps();
        $manager = $this->createCampManager($campA);

        $ownHousehold = $this->createHousehold($campA, 'Camp A Head', '123456701');
        $this->createHousehold($campB, 'Camp B Head', '123456702');

        $response = $this->actingAs($manager)->get(route('admin.households.index'));

        $response->assertOk();
        $response->assertSee($ownHousehold->head_name);
        $response->assertDontSee('Camp B Head');
    }

    public function test_camp_manager_cannot_view_or_verify_households_from_other_camps(): void
    {
        [$campA, $campB] = $this->createCamps();
        $manager = $this->createCampManager($campA);
        $foreignHousehold = $this->createHousehold($campB, 'Foreign Head', '123456703');

        $this->actingAs($manager)
            ->get(route('admin.households.show', $foreignHousehold))
            ->assertForbidden();

        $this->actingAs($manager)
            ->post(route('admin.households.verify', $foreignHousehold))
            ->assertForbidden();
    }

    public function test_camp_manager_can_verify_household_in_their_camp(): void
    {
        [$campA] = $this->createCamps();
        $manager = $this->createCampManager($campA);
        $household = $this->createHousehold($campA, 'Pending Head', '123456704', 'pending');

        $this->actingAs($manager)
            ->from(route('admin.households.index'))
            ->post(route('admin.households.verify', $household))
            ->assertRedirect(route('admin.households.index'));

        $this->assertDatabaseHas('households', [
            'id' => $household->id,
            'status' => 'verified',
        ]);
    }

    public function test_camp_manager_distribution_search_is_scoped_to_their_camp(): void
    {
        [$campA, $campB] = $this->createCamps();
        $manager = $this->createCampManager($campA);

        $ownHousehold = $this->createHousehold($campA, 'Shared Query Alpha', '123456705');
        $foreignHousehold = $this->createHousehold($campB, 'Shared Query Beta', '123456706');

        $response = $this->actingAs($manager)
            ->getJson(route('admin.distributions.search-household', ['q' => 'Shared Query']));

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $ownHousehold->id,
            'head_name' => $ownHousehold->head_name,
        ]);
        $response->assertJsonMissing([
            'id' => $foreignHousehold->id,
            'head_name' => $foreignHousehold->head_name,
        ]);
    }

    public function test_households_export_for_camp_manager_only_contains_their_camp_records(): void
    {
        [$campA, $campB] = $this->createCamps();
        $manager = $this->createCampManager($campA);

        $ownHousehold = $this->createHousehold($campA, 'Export Camp A', '123456707');
        $this->createHousehold($campB, 'Export Camp B', '123456708');

        $request = Request::create('/admin/import-export/export-households', 'GET', [
            'region_id' => $campB->id,
        ]);

        $export = new HouseholdsExport($request, $manager);
        $filePath = $export->generate();

        try {
            $sheet = IOFactory::load($filePath)->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
            $exportedHeadNames = collect(array_slice($rows, 1))
                ->pluck('B')
                ->filter()
                ->values()
                ->all();

            $this->assertSame([$ownHousehold->head_name], $exportedHeadNames);
        } finally {
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
    }

    public function test_camp_manager_import_rejects_rows_for_other_camps(): void
    {
        [$campA, $campB] = $this->createCamps();
        $manager = $this->createCampManager($campA);

        $import = new HouseholdsImport($manager);
        $import->collection(new Collection([
            collect([
                'region' => $campB->name,
                'national_id' => '123456709',
            ]),
        ]));

        $this->assertSame(0, $import->getSuccessCount());
        $this->assertSame(1, $import->getFailureCount());
        $this->assertDatabaseMissing('households', [
            'head_national_id' => '123456709',
        ]);
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
            'name' => 'Camp A',
            'code' => 'CAMP-A',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        $campB = Region::create([
            'name' => 'Camp B',
            'code' => 'CAMP-B',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        return [$campA, $campB];
    }

    private function createCampManager(Region $camp): User
    {
        $manager = User::create([
            'name' => 'Camp Manager User',
            'national_id' => '900000010',
            'phone' => '0500000010',
            'password' => Hash::make('password'),
            'is_staff' => true,
            'region_id' => $camp->id,
        ]);

        $manager->assignRole('camp_manager');

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
