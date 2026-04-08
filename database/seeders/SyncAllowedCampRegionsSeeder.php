<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class SyncAllowedCampRegionsSeeder extends Seeder
{
    private const CAMP_PARENT_NAME = 'المخيمات';
    private const CAMP_PARENT_CODE = 'CAMP-REGIONS';

    private const CAMPS = [
        'مخيم الابرار(الغفران)' => 'CAMP-ABRAR-GHFRAN',
        'مخيم الامام مالك بن انس' => 'CAMP-IMAM-MALIK',
        'مخيم ام القرى' => 'CAMP-UMM-ALQURA',
        'مخيم عثمان بن عفان' => 'CAMP-UTHMAN-AFFAN',
        'مخيم الايمان' => 'CAMP-IMAN',
        'مخيم الصمود' => 'CAMP-SUMUD',
        'مخيم النور' => 'CAMP-NOOR',
        'مخيم المسمكة' => 'CAMP-MASMAKA',
        'مخيم الصابرين' => 'CAMP-SABIREEN',
        'مخيم الصديق' => 'CAMP-SEDEEQ',
        'مخيم المطاحن' => 'CAMP-MATAHIN',
        'مخيم حديقة الامل' => 'CAMP-HADIQAT-ALAMAL',
    ];

    public function run(): void
    {
        $parent = Region::query()->firstOrCreate(
            ['code' => self::CAMP_PARENT_CODE],
            [
                'name' => self::CAMP_PARENT_NAME,
                'is_active' => true,
            ]
        );

        $parent->forceFill([
            'name' => self::CAMP_PARENT_NAME,
            'is_active' => true,
        ])->save();

        foreach (self::CAMPS as $name => $code) {
            $region = Region::query()
                ->where('code', $code)
                ->orWhere('name', $name)
                ->first();

            if (! $region) {
                $region = new Region();
            }

            $region->name = $name;
            $region->code = $code;
            $region->parent_id = $parent->id;
            $region->is_active = true;
            $region->save();
        }
    }
}
