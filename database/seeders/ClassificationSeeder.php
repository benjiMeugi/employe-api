<?php

namespace Database\Seeders;

use App\Models\Classification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $direction = Classification::create([
            'label' => 'Direction',
            'code' => 'DIRECTION',
            'order' => 1,
        ]);

        $cadreSuperieur = Classification::create([
            'label' => 'Cadre supérieur',
            'code' => 'CADRE_SUP',
            'order' => 2,
            'parent_id' => $direction->id,
        ]);

        $cadre = Classification::create([
            'label' => 'Cadre',
            'code' => 'CADRE',
            'order' => 3,
            'parent_id' => $cadreSuperieur->id,
        ]);

        $employe = Classification::create([
            'label' => 'Employé',
            'code' => 'EMPLOYE',
            'order' => 4,
            'parent_id' => $cadre->id,
        ]);

        Classification::create([
            'label' => 'Manager',
            'code' => 'MANAGER',
            'order' => 1,
            'parent_id' => $cadreSuperieur->id,
        ]);

        Classification::create([
            'label' => 'Responsable',
            'code' => 'RESPONSABLE',
            'order' => 2,
            'parent_id' => $cadreSuperieur->id,
        ]);

        Classification::create([
            'label' => 'Ingénieur',
            'code' => 'INGENIEUR',
            'order' => 1,
            'parent_id' => $cadre->id,
        ]);

        Classification::create([
            'label' => 'Analyste',
            'code' => 'ANALYSTE',
            'order' => 2,
            'parent_id' => $cadre->id,
        ]);

        Classification::create([
            'label' => 'Assistant',
            'code' => 'ASSISTANT',
            'order' => 1,
            'parent_id' => $employe->id,
        ]);

        Classification::create([
            'label' => 'Technicien',
            'code' => 'TECHNICIEN',
            'order' => 2,
            'parent_id' => $employe->id,
        ]);
    }
}
