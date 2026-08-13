<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $direction = Unit::create([
            'code' => 'DG',
            'label' => 'Direction Générale',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $rh = Unit::create([
            'code' => 'RH',
            'label' => 'Ressources Humaines',
            'parent_id' => $direction->id,
            'is_active' => true,
        ]);

        $technique = Unit::create([
            'code' => 'DTQ',
            'label' => 'Direction Technique',
            'parent_id' => $direction->id,
            'is_active' => true,
        ]);

        Unit::create([
            'code' => 'MAINT',
            'label' => 'Maintenance',
            'parent_id' => $technique->id,
            'is_active' => true,
        ]);

        Unit::create([
            'code' => 'RECRUT',
            'label' => 'Recrutement',
            'parent_id' => $rh->id,
            'is_active' => true,
        ]);
    }
}
