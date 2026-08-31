<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            // id partagé avec career_events — d'où viennent employee_id
            // et event_date, jamais dupliqués ici.
            $table->foreignId('id')->primary()->constrained('career_events')->cascadeOnDelete();

            $table->foreignId('absence_type_id')->constrained('absence_types')->restrictOnDelete();
            $table->foreignId('absence_request_id')->nullable()
                ->constrained('absence_requests')->nullOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            // Une seule des deux est renseignée, selon duration_unit
            // du type. Les permissions horaires ne consomment aucun
            // solde — les deux unités ne se croisent jamais.
            $table->integer('duration_days')->nullable();
            $table->integer('duration_hours')->nullable();

            // Décision RH, prise absence par absence : l'excédent au-delà
            // du quota part-il sur le congé annuel, ou est-il offert ?
            $table->boolean('excess_is_deductible')->default(false);

            // Figé au moment de l'imputation, jamais recalculé — si
            // quota_days change plus tard, ce qui a été prélevé le reste.
            $table->decimal('deducted_days', 6, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
