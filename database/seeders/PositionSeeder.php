<?php

namespace Database\Seeders;

use App\Models\Classification;
use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $direction = Classification::where('code', 'DIRECTION')->firstOrFail();
        $cadreSuperieur = Classification::where('code', 'CADRE_SUP')->firstOrFail();
        $cadre = Classification::where('code', 'CADRE')->firstOrFail();
        $employe = Classification::where('code', 'EMPLOYE')->firstOrFail();

        $positions = [
            [
                'label' => 'Directeur des ressources humaines',
                'code' => 'DRH',
                'classification_id' => $direction->id,
                'planned_headcount' => 1,
            ],
            [
                'label' => 'Directeur financier',
                'code' => 'DF',
                'classification_id' => $direction->id,
                'planned_headcount' => 1,
            ],
            [
                'label' => 'Responsable RH',
                'code' => 'RESP_RH',
                'classification_id' => $cadreSuperieur->id,
                'planned_headcount' => 3,
            ],
            [
                'label' => 'Responsable financier',
                'code' => 'RESP_FIN',
                'classification_id' => $cadreSuperieur->id,
                'planned_headcount' => 2,
            ],
            [
                'label' => 'Ingénieur logiciel',
                'code' => 'ING_LOG',
                'classification_id' => $cadre->id,
                'planned_headcount' => 10,
            ],
            [
                'label' => 'Analyste financier',
                'code' => 'ANALYSTE_FIN',
                'classification_id' => $cadre->id,
                'planned_headcount' => 5,
            ],
            [
                'label' => 'Assistant RH',
                'code' => 'ASSIST_RH',
                'classification_id' => $employe->id,
                'planned_headcount' => 5,
            ],
            [
                'label' => 'Technicien informatique',
                'code' => 'TECH_INFO',
                'classification_id' => $employe->id,
                'planned_headcount' => 8,
            ],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['code' => $position['code']],
                $position
            );
        }
    }
}
