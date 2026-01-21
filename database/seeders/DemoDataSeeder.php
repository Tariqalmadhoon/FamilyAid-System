<?php

namespace Database\Seeders;

use App\Models\AidProgram;
use App\Models\Distribution;
use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed demo data for testing.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['national_id' => 'ADMIN001'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'phone' => '0500000001',
                'is_staff' => true,
                'security_question' => 'What is your favorite color?',
                'security_answer_hash' => Hash::make('blue'),
            ]
        );
        $admin->assignRole('admin');

        // Create data entry user
        $dataEntry = User::firstOrCreate(
            ['national_id' => 'DATA001'],
            [
                'name' => 'Data Entry Staff',
                'password' => Hash::make('password'),
                'phone' => '0500000002',
                'is_staff' => true,
                'security_question' => 'What is your favorite color?',
                'security_answer_hash' => Hash::make('green'),
            ]
        );
        $dataEntry->assignRole('data_entry');

        // Create distributor user
        $distributor = User::firstOrCreate(
            ['national_id' => 'DIST001'],
            [
                'name' => 'Field Distributor',
                'password' => Hash::make('password'),
                'phone' => '0500000003',
                'is_staff' => true,
                'security_question' => 'What is your favorite color?',
                'security_answer_hash' => Hash::make('red'),
            ]
        );
        $distributor->assignRole('distributor');

        // Get regions
        $regions = Region::whereNotNull('parent_id')->get();

        // Create Aid Programs
        $programs = [
            ['name' => 'Ramadan Food Basket 2026', 'description' => 'Monthly food basket distribution during Ramadan', 'is_active' => true],
            ['name' => 'Winter Clothing Support', 'description' => 'Winter clothes and blankets for families in need', 'is_active' => true],
            ['name' => 'Eid Al-Adha Meat Distribution', 'description' => 'Fresh meat distribution during Eid Al-Adha', 'is_active' => true],
            ['name' => 'School Supplies 2026', 'description' => 'Back to school supplies for children', 'is_active' => false],
        ];

        foreach ($programs as $programData) {
            AidProgram::firstOrCreate(['name' => $programData['name']], $programData);
        }

        $activePrograms = AidProgram::active()->get();

        // Create sample households
        $households = [
            ['head_national_id' => '1234567001', 'head_name' => 'Ahmed Mohammed', 'housing_type' => 'rented', 'status' => 'verified'],
            ['head_national_id' => '1234567002', 'head_name' => 'Fatima Ali', 'housing_type' => 'family_hosted', 'status' => 'verified'],
            ['head_national_id' => '1234567003', 'head_name' => 'Omar Hassan', 'housing_type' => 'owned', 'status' => 'pending'],
            ['head_national_id' => '1234567004', 'head_name' => 'Aisha Ibrahim', 'housing_type' => 'rented', 'status' => 'verified'],
            ['head_national_id' => '1234567005', 'head_name' => 'Khalid Youssef', 'housing_type' => 'rented', 'status' => 'pending'],
            ['head_national_id' => '1234567006', 'head_name' => 'Maryam Saleh', 'housing_type' => 'family_hosted', 'status' => 'verified'],
            ['head_national_id' => '1234567007', 'head_name' => 'Salem Abdullah', 'housing_type' => 'owned', 'status' => 'verified'],
            ['head_national_id' => '1234567008', 'head_name' => 'Noura Rashid', 'housing_type' => 'rented', 'status' => 'pending'],
        ];

        $memberRelations = ['spouse', 'son', 'daughter', 'parent', 'sibling'];
        $householdModels = [];

        foreach ($households as $index => $hData) {
            $region = $regions[$index % $regions->count()];
            
            $household = Household::firstOrCreate(
                ['head_national_id' => $hData['head_national_id']],
                array_merge($hData, [
                    'region_id' => $region->id,
                    'address_text' => fake()->address(),
                    'primary_phone' => '050' . fake()->numerify('#######'),
                ])
            );

            $householdModels[] = $household;

            // Add 1-4 members per household
            $memberCount = rand(1, 4);
            for ($i = 0; $i < $memberCount; $i++) {
                HouseholdMember::firstOrCreate(
                    [
                        'household_id' => $household->id,
                        'full_name' => fake()->name(),
                    ],
                    [
                        'relation_to_head' => $memberRelations[array_rand($memberRelations)],
                        'gender' => ['male', 'female'][rand(0, 1)],
                        'birth_date' => fake()->date('Y-m-d', '-5 years'),
                    ]
                );
            }
        }

        // Create distributions for verified households
        $verifiedHouseholds = Household::verified()->get();
        
        foreach ($verifiedHouseholds as $household) {
            // Give each verified household 1-2 distributions
            $numDist = rand(1, 2);
            $usedPrograms = [];

            for ($i = 0; $i < $numDist; $i++) {
                $program = $activePrograms->whereNotIn('id', $usedPrograms)->random();
                if (!$program) continue;

                $usedPrograms[] = $program->id;

                Distribution::firstOrCreate(
                    [
                        'household_id' => $household->id,
                        'aid_program_id' => $program->id,
                    ],
                    [
                        'distribution_date' => fake()->dateTimeBetween('-2 months', 'now'),
                        'distributed_by' => $distributor->id,
                        'notes' => rand(0, 1) ? 'Regular distribution' : null,
                    ]
                );
            }
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Admin login: ADMIN001 / password');
        $this->command->info('Data Entry login: DATA001 / password');
        $this->command->info('Distributor login: DIST001 / password');
    }
}
