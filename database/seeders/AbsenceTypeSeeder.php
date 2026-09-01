<?php

namespace Database\Seeders;

use App\Models\AbsenceType;
use Illuminate\Database\Seeder;

class AbsenceTypeSeeder extends Seeder
{
    /**
     * Valeurs de départ, à ajuster selon le règlement intérieur réel —
     * notamment quota_days et quota_basis, qui relèvent du droit du
     * travail et non d'un choix technique.
     */
    public function run(): void
    {
        // Le seul type qui s'accumule : 2,5 j par mois d'ancienneté.
        // quota_days = 30 reste la référence légale affichée, l'acquis
        // réel vient des octrois mensuels.
        AbsenceType::updateOrCreate(['code' => 'CA'], [
            'label' => 'Congé annuel',
            'is_paid' => true,
            'is_calendar_based' => true,
            'duration_unit' => 'day',
            'gender_restriction' => 'none',
            'quota_days' => 30,
            'quota_basis' => 'per_period',
            'accrual_rate_per_month' => 2.5,
            'expiration_delay_months' => 3,
            'expiration_mode' => 'per_year',
            'requires_supporting_document' => false,
        ]);

        AbsenceType::updateOrCreate(['code' => 'MAT'], [
            'label' => 'Congé maternité',
            'is_paid' => true,
            'is_calendar_based' => true,
            'duration_unit' => 'day',
            'gender_restriction' => 'F',
            'quota_days' => 98,
            'quota_basis' => 'per_occurrence',
            'accrual_rate_per_month' => 0,
            'expiration_delay_months' => null,
            'expiration_mode' => null,
            'requires_supporting_document' => true,
        ]);

        AbsenceType::updateOrCreate(['code' => 'PAT'], [
            'label' => 'Congé paternité',
            'is_paid' => true,
            'is_calendar_based' => true,
            'duration_unit' => 'day',
            'gender_restriction' => 'M',
            'quota_days' => 3,
            'quota_basis' => 'per_occurrence',
            'accrual_rate_per_month' => 0,
            'expiration_delay_months' => null,
            'expiration_mode' => null,
            'requires_supporting_document' => true,
        ]);

        AbsenceType::updateOrCreate(['code' => 'CM'], [
            'label' => 'Congé maladie',
            'is_paid' => true,
            'is_calendar_based' => true,
            'duration_unit' => 'day',
            'gender_restriction' => 'none',
            'quota_days' => 0,
            'quota_basis' => 'per_occurrence',
            'accrual_rate_per_month' => 0,
            'expiration_delay_months' => null,
            'expiration_mode' => null,
            'requires_supporting_document' => true,
        ]);

        // À partir d'ici : des permissions (is_calendar_based = false).
        // Elles n'apparaissent pas dans la vue de solde, et seuls les
        // jours ouvrés comptent dans leur durée.

        AbsenceType::updateOrCreate(['code' => 'MAR'], [
            'label' => 'Permission mariage',
            'is_paid' => true,
            'is_calendar_based' => false,
            'duration_unit' => 'day',
            'gender_restriction' => 'none',
            'quota_days' => 3,
            'quota_basis' => 'per_occurrence',
            'accrual_rate_per_month' => 0,
            'expiration_delay_months' => null,
            'expiration_mode' => null,
            'requires_supporting_document' => true,
        ]);

        AbsenceType::updateOrCreate(['code' => 'DEC'], [
            'label' => 'Permission décès',
            'is_paid' => true,
            'is_calendar_based' => false,
            'duration_unit' => 'day',
            'gender_restriction' => 'none',
            'quota_days' => 3,
            'quota_basis' => 'per_occurrence',
            'accrual_rate_per_month' => 0,
            'expiration_delay_months' => null,
            'expiration_mode' => null,
            'requires_supporting_document' => true,
        ]);

        AbsenceType::updateOrCreate(['code' => 'PEX'], [
            'label' => 'Permission exceptionnelle',
            'is_paid' => true,
            'is_calendar_based' => false,
            'duration_unit' => 'day',
            'gender_restriction' => 'none',
            'quota_days' => 5,
            'quota_basis' => 'per_period',
            'accrual_rate_per_month' => 0,
            'expiration_delay_months' => null,
            'expiration_mode' => null,
            'requires_supporting_document' => false,
        ]);

        // Permission de quelques heures : ne consomme aucun solde,
        // n'entre dans aucun calcul de jours.
        AbsenceType::updateOrCreate(['code' => 'PH'], [
            'label' => 'Permission horaire',
            'is_paid' => true,
            'is_calendar_based' => false,
            'duration_unit' => 'hour',
            'gender_restriction' => 'none',
            'quota_days' => 0,
            'quota_basis' => 'per_occurrence',
            'accrual_rate_per_month' => 0,
            'expiration_delay_months' => null,
            'expiration_mode' => null,
            'requires_supporting_document' => false,
        ]);

        AbsenceType::updateOrCreate(['code' => 'CSS'], [
            'label' => 'Congé sans solde',
            'is_paid' => false,
            'is_calendar_based' => true,
            'duration_unit' => 'day',
            'gender_restriction' => 'none',
            'quota_days' => 0,
            'quota_basis' => 'per_occurrence',
            'accrual_rate_per_month' => 0,
            'expiration_delay_months' => null,
            'expiration_mode' => null,
            'requires_supporting_document' => false,
        ]);
    }
}
