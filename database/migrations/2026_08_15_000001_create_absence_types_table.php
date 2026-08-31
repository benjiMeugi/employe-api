<?php

use App\Models\AbsenceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absence_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->boolean('is_paid')->default(true);

            // true  : congé — weekends et fériés comptent dans la durée
            // false : permission — seuls les jours ouvrés comptent
            $table->boolean('is_calendar_based')->default(false);

            // day  : durée saisie en jours
            // hour : durée saisie en heures (permissions de quelques heures,
            //        qui ne consomment jamais de solde)
            $table->enum('duration_unit', AbsenceType::$DURATION_UNIT_OPTIONS)
                ->default(AbsenceType::$DURATION_UNIT_OPTIONS[0]);

            // none : ouvert à tous — M / F : réservé (paternité, maternité)
            $table->enum('gender_restriction', AbsenceType::$GENDER_RESTRICTION_OPTIONS)
                ->default(AbsenceType::$GENDER_RESTRICTION_OPTIONS[0]);

            $table->decimal('quota_days', 6, 2)->default(0);
            $table->enum('quota_basis', AbsenceType::$QUOTA_BASIS_OPTIONS)
                ->default(AbsenceType::$QUOTA_BASIS_OPTIONS[0]);

            // > 0 ⟹ ce type s'accumule mensuellement (congé annuel)
            $table->decimal('accrual_rate_per_month', 5, 2)->default(0);

            $table->integer('expiration_delay_months')->nullable();
            $table->enum('expiration_mode', AbsenceType::$EXPIRATION_MODE_OPTIONS)->nullable();

            $table->boolean('requires_supporting_document')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_types');
    }
};
