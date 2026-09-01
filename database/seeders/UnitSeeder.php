<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $direction = Unit::updateOrCreate(
            ['code' => 'DG'],
            [
                'label' => 'Direction Générale',
                'parent_id' => null,
                'is_active' => true,
            ]
        );

        $rh = Unit::updateOrCreate(
            ['code' => 'RH'],
            [
                'label' => 'Ressources Humaines',
                'parent_id' => $direction->id,
                'is_active' => true,
            ]
        );

        $technique = Unit::updateOrCreate(
            ['code' => 'DTQ'],
            [
                'label' => 'Direction Technique',
                'parent_id' => $direction->id,
                'is_active' => true,
            ]
        );

        Unit::updateOrCreate(
            ['code' => 'MAINT'],
            [
                'label' => 'Maintenance',
                'parent_id' => $technique->id,
                'is_active' => true,
            ]
        );

        Unit::updateOrCreate(
            ['code' => 'RECRUT'],
            [
                'label' => 'Recrutement',
                'parent_id' => $rh->id,
                'is_active' => true,
            ]
        );

        $this->command->info('Unités créées avec succès.');
    }
}
