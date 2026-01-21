<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main regions
        $regions = [
            [
                'name' => 'North Region',
                'code' => 'NORTH',
                'children' => [
                    ['name' => 'North District 1', 'code' => 'NORTH-1'],
                    ['name' => 'North District 2', 'code' => 'NORTH-2'],
                    ['name' => 'North District 3', 'code' => 'NORTH-3'],
                ],
            ],
            [
                'name' => 'South Region',
                'code' => 'SOUTH',
                'children' => [
                    ['name' => 'South District 1', 'code' => 'SOUTH-1'],
                    ['name' => 'South District 2', 'code' => 'SOUTH-2'],
                ],
            ],
            [
                'name' => 'East Region',
                'code' => 'EAST',
                'children' => [
                    ['name' => 'East District 1', 'code' => 'EAST-1'],
                    ['name' => 'East District 2', 'code' => 'EAST-2'],
                ],
            ],
            [
                'name' => 'West Region',
                'code' => 'WEST',
                'children' => [
                    ['name' => 'West District 1', 'code' => 'WEST-1'],
                    ['name' => 'West District 2', 'code' => 'WEST-2'],
                ],
            ],
            [
                'name' => 'Central Region',
                'code' => 'CENTRAL',
                'children' => [
                    ['name' => 'Central District 1', 'code' => 'CENTRAL-1'],
                    ['name' => 'Central District 2', 'code' => 'CENTRAL-2'],
                    ['name' => 'Central District 3', 'code' => 'CENTRAL-3'],
                    ['name' => 'Central District 4', 'code' => 'CENTRAL-4'],
                ],
            ],
        ];

        foreach ($regions as $regionData) {
            $children = $regionData['children'] ?? [];
            unset($regionData['children']);

            $region = Region::create($regionData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $region->id;
                Region::create($childData);
            }
        }
    }
}
