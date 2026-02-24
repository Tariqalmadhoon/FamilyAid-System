<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $parentId = DB::table('regions')
            ->where('code', 'CAMP-REGIONS')
            ->value('id');

        $regions = [
            ['name' => 'مخيم الصديق', 'code' => 'CAMP-SEDEEQ'],
            ['name' => 'مخيم المطاحن', 'code' => 'CAMP-MATAHIN'],
        ];

        foreach ($regions as $region) {
            $existing = DB::table('regions')
                ->where('code', $region['code'])
                ->orWhere('name', $region['name'])
                ->first();

            if ($existing) {
                DB::table('regions')
                    ->where('id', $existing->id)
                    ->update([
                        'name' => $region['name'],
                        'code' => $region['code'],
                        'parent_id' => $parentId ?? $existing->parent_id,
                        'is_active' => true,
                        'updated_at' => now(),
                    ]);

                continue;
            }

            DB::table('regions')->insert([
                'name' => $region['name'],
                'code' => $region['code'],
                'parent_id' => $parentId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('regions')
            ->whereIn('code', ['CAMP-SEDEEQ', 'CAMP-MATAHIN'])
            ->delete();
    }
};

