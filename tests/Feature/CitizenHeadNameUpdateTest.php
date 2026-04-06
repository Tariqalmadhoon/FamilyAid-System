<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\Region;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CitizenHeadNameUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Region $region;

    protected function setUp(): void
    {
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = ':memory:';
        $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_DATABASE'] = ':memory:';

        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->region = Region::create([
            'name' => Region::ALLOWED_CAMP_REGION_NAMES[0],
            'parent_id' => null,
            'code' => 'TEST-CAMP-1',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    protected function makeCitizen(): User
    {
        $user = User::create([
            'name' => 'Original Citizen Name',
            'first_name' => 'Original',
            'father_name' => 'Citizen',
            'grandfather_name' => 'Tester',
            'last_name' => 'Name',
            'birth_date' => '1990-01-15',
            'national_id' => '123456789',
            'phone' => '0591234567',
            'password' => Hash::make('password'),
            'is_staff' => false,
        ]);

        $user->assignRole('citizen');

        return $user;
    }

    protected function makeHousehold(User $user, array $overrides = []): Household
    {
        $household = Household::create(array_merge([
            'head_national_id' => $user->national_id,
            'head_name' => 'Original Citizen Name',
            'head_birth_date' => '1990-01-15',
            'region_id' => $this->region->id,
            'address_text' => 'Old Address 12',
            'previous_governorate' => 'north_gaza',
            'previous_area' => 'jabalia',
            'housing_type' => 'owned',
            'primary_phone' => '0591234567',
            'secondary_phone' => '0597654321',
            'status' => 'pending',
            'has_war_injury' => false,
            'has_chronic_disease' => false,
            'has_disability' => false,
            'condition_type' => null,
            'condition_notes' => null,
            'payment_account_type' => 'wallet',
            'payment_account_number' => '0591234567',
            'payment_account_holder_name' => 'Original Citizen Name',
            'spouse_full_name' => 'Spouse Full Name',
            'spouse_national_id' => '987654321',
            'spouse_birth_date' => '1992-03-10',
            'spouse_has_war_injury' => false,
            'spouse_has_chronic_disease' => false,
            'spouse_has_disability' => false,
            'spouse_condition_type' => null,
            'spouse_health_notes' => null,
            'citizen_head_name_updated_at' => null,
        ], $overrides));

        $user->forceFill(['household_id' => $household->id])->save();

        return $household;
    }

    protected function payload(Household $household, array $overrides = []): array
    {
        return array_merge([
            'head_name' => $household->head_name,
            'region_id' => $household->region_id,
            'address_text' => $household->address_text,
            'spouse_full_name' => $household->spouse_full_name,
            'spouse_national_id' => $household->spouse_national_id,
            'spouse_birth_date' => optional($household->spouse_birth_date)->toDateString(),
            'spouse_has_war_injury' => (int) $household->spouse_has_war_injury,
            'spouse_has_chronic_disease' => (int) $household->spouse_has_chronic_disease,
            'spouse_has_disability' => (int) $household->spouse_has_disability,
            'spouse_condition_type' => $household->spouse_condition_type,
            'spouse_health_notes' => $household->spouse_health_notes,
            'payment_account_type' => $household->payment_account_type,
            'payment_account_number' => $household->payment_account_number,
            'payment_account_holder_name' => $household->payment_account_holder_name,
            'housing_type' => $household->housing_type,
            'primary_phone' => $household->primary_phone,
            'secondary_phone' => $household->secondary_phone,
            'has_war_injury' => (int) $household->has_war_injury,
            'has_chronic_disease' => (int) $household->has_chronic_disease,
            'has_disability' => (int) $household->has_disability,
            'condition_type' => $household->condition_type,
            'condition_notes' => $household->condition_notes,
        ], $overrides);
    }

    public function test_citizen_can_update_head_name_once_and_it_is_audited(): void
    {
        Carbon::setTestNow('2026-04-06 09:30:00');

        $user = $this->makeCitizen();
        $household = $this->makeHousehold($user);

        $response = $this->actingAs($user)->from(route('citizen.household.edit'))->put(
            route('citizen.household.update'),
            $this->payload($household, ['head_name' => 'Updated Citizen Name'])
        );

        $response->assertRedirect(route('citizen.household.edit'));

        $household->refresh();

        $this->assertSame('Updated Citizen Name', $household->head_name);
        $this->assertNotNull($household->citizen_head_name_updated_at);
        $this->assertTrue($household->citizen_head_name_updated_at->equalTo(now()));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'update',
            'entity_type' => 'Household',
            'entity_id' => $household->id,
        ]);
    }

    public function test_citizen_cannot_change_head_name_twice_in_the_same_month(): void
    {
        Carbon::setTestNow('2026-04-20 11:00:00');

        $user = $this->makeCitizen();
        $household = $this->makeHousehold($user, [
            'head_name' => 'Current Saved Name',
            'citizen_head_name_updated_at' => '2026-04-03 08:00:00',
        ]);

        $response = $this->actingAs($user)->from(route('citizen.household.edit'))->put(
            route('citizen.household.update'),
            $this->payload($household, [
                'head_name' => 'Second Change In April',
                'address_text' => 'Changed Address Should Not Save',
            ])
        );

        $response->assertRedirect(route('citizen.household.edit'));
        $response->assertSessionHasErrors(['head_name']);

        $household->refresh();

        $this->assertSame('Current Saved Name', $household->head_name);
        $this->assertSame('Old Address 12', $household->address_text);
    }

    public function test_citizen_can_still_update_other_fields_when_name_is_locked_but_unchanged(): void
    {
        Carbon::setTestNow('2026-04-20 12:00:00');

        $user = $this->makeCitizen();
        $household = $this->makeHousehold($user, [
            'head_name' => 'Locked Saved Name',
            'citizen_head_name_updated_at' => '2026-04-01 07:00:00',
        ]);

        $response = $this->actingAs($user)->from(route('citizen.household.edit'))->put(
            route('citizen.household.update'),
            $this->payload($household, [
                'head_name' => 'Locked Saved Name',
                'primary_phone' => '0590001111',
            ])
        );

        $response->assertRedirect(route('citizen.household.edit'));
        $response->assertSessionDoesntHaveErrors();

        $household->refresh();

        $this->assertSame('Locked Saved Name', $household->head_name);
        $this->assertSame('0590001111', $household->primary_phone);
        $this->assertSame('2026-04-01 07:00:00', $household->citizen_head_name_updated_at?->format('Y-m-d H:i:s'));
    }

    public function test_citizen_can_change_head_name_again_in_a_new_month(): void
    {
        Carbon::setTestNow('2026-05-02 10:00:00');

        $user = $this->makeCitizen();
        $household = $this->makeHousehold($user, [
            'head_name' => 'April Saved Name',
            'citizen_head_name_updated_at' => '2026-04-10 09:00:00',
        ]);

        $response = $this->actingAs($user)->from(route('citizen.household.edit'))->put(
            route('citizen.household.update'),
            $this->payload($household, ['head_name' => 'May Saved Name'])
        );

        $response->assertRedirect(route('citizen.household.edit'));

        $household->refresh();

        $this->assertSame('May Saved Name', $household->head_name);
        $this->assertSame('2026-05-02 10:00:00', $household->citizen_head_name_updated_at?->format('Y-m-d H:i:s'));
    }
}
