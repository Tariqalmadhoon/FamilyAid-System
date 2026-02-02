<?php

namespace Tests\Feature;

use App\Models\Region;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class OnboardingValidationTest extends TestCase
{
    use RefreshDatabase;

    protected Region $region;

    protected function setUp(): void
    {
        // Ensure the application boots with a fast in-memory sqlite database before migrations run.
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
            'name' => 'Central',
            'parent_id' => null,
            'code' => Str::random(5),
            'is_active' => true,
        ]);
    }

    protected function makeCitizen(): User
    {
        $user = User::create([
            'name' => 'Citizen Tester',
            'national_id' => '123456789',
            'phone' => '1234567890',
            'password' => Hash::make('password'),
            'is_staff' => false,
        ]);

        $user->assignRole('citizen');

        return $user;
    }

    protected function basePayload(array $overrides = []): array
    {
        return array_merge([
            'region_id' => $this->region->id,
            'address_text' => '123 Test Street',
            'housing_type' => 'owned',
            'primary_phone' => '1234567890',
            'secondary_phone' => null,
            'has_war_injury' => 0,
            'has_chronic_disease' => 0,
            'has_disability' => 0,
            'condition_type' => '',
            'condition_notes' => '',
            'members' => [],
        ], $overrides);
    }

    public function test_condition_type_not_required_when_no_health_flags(): void
    {
        $user = $this->makeCitizen();
        $payload = $this->basePayload();

        $response = $this->actingAs($user)->post(route('citizen.onboarding.store'), $payload);

        $response->assertRedirect(route('citizen.dashboard'));

        $this->assertDatabaseHas('households', [
            'head_national_id' => $user->national_id,
            'condition_type' => null,
        ]);
    }

    public function test_condition_type_required_when_any_household_flag_set(): void
    {
        $user = $this->makeCitizen();
        $payload = $this->basePayload([
            'has_war_injury' => 1,
            'condition_type' => '',
        ]);

        $response = $this->actingAs($user)->from(route('citizen.onboarding'))->post(route('citizen.onboarding.store'), $payload);

        $response->assertSessionHasErrors(['condition_type']);
        $this->assertDatabaseCount('households', 0);
    }

    public function test_condition_type_saved_when_flag_and_value_present(): void
    {
        $user = $this->makeCitizen();
        $payload = $this->basePayload([
            'has_chronic_disease' => 1,
            'condition_type' => 'Diabetes',
        ]);

        $response = $this->actingAs($user)->post(route('citizen.onboarding.store'), $payload);

        $response->assertRedirect(route('citizen.dashboard'));

        $this->assertDatabaseHas('households', [
            'head_national_id' => $user->national_id,
            'condition_type' => 'Diabetes',
        ]);
    }

    public function test_condition_type_cleared_when_flags_are_off(): void
    {
        $user = $this->makeCitizen();
        $payload = $this->basePayload([
            'has_disability' => 0,
            'condition_type' => 'Legacy Value',
        ]);

        $response = $this->actingAs($user)->post(route('citizen.onboarding.store'), $payload);

        $response->assertRedirect(route('citizen.dashboard'));

        $this->assertDatabaseHas('households', [
            'head_national_id' => $user->national_id,
            'condition_type' => null,
        ]);
    }

    public function test_member_condition_required_per_member_flags(): void
    {
        $user = $this->makeCitizen();
        $payload = $this->basePayload([
            'members' => [
                [
                    'full_name' => 'Member One',
                    'relation_to_head' => 'son',
                    'national_id' => null,
                    'has_war_injury' => 1,
                    'has_chronic_disease' => 0,
                    'has_disability' => 0,
                    'condition_type' => '',
                    'health_notes' => '',
                ],
                [
                    'full_name' => 'Member Two',
                    'relation_to_head' => 'daughter',
                    'national_id' => null,
                    'has_war_injury' => 0,
                    'has_chronic_disease' => 0,
                    'has_disability' => 0,
                    'condition_type' => '',
                    'health_notes' => '',
                ],
            ],
        ]);

        $response = $this->actingAs($user)->from(route('citizen.onboarding'))->post(route('citizen.onboarding.store'), $payload);

        $response->assertSessionHasErrors(['members.0.condition_type']);
        $this->assertDatabaseCount('households', 0);
    }

    public function test_member_condition_saved_when_flagged_with_value(): void
    {
        $user = $this->makeCitizen();
        $payload = $this->basePayload([
            'members' => [
                [
                    'full_name' => 'Member One',
                    'relation_to_head' => 'son',
                    'national_id' => null,
                    'has_war_injury' => 0,
                    'has_chronic_disease' => 1,
                    'has_disability' => 0,
                    'condition_type' => 'Asthma',
                    'health_notes' => '',
                ],
                [
                    'full_name' => 'Member Two',
                    'relation_to_head' => 'daughter',
                    'national_id' => null,
                    'has_war_injury' => 0,
                    'has_chronic_disease' => 0,
                    'has_disability' => 0,
                    'condition_type' => '',
                    'health_notes' => '',
                ],
            ],
        ]);

        $response = $this->actingAs($user)->post(route('citizen.onboarding.store'), $payload);

        $response->assertRedirect(route('citizen.dashboard'));

        $this->assertDatabaseHas('household_members', [
            'full_name' => 'Member One',
            'condition_type' => 'Asthma',
        ]);

        $this->assertDatabaseHas('household_members', [
            'full_name' => 'Member Two',
            'condition_type' => null,
        ]);
    }
}
