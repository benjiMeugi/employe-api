<?php

use App\Models\AbsenceRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absence_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('employes')->nullOnDelete();
            $table->foreignId('absence_type_id')->constrained('absence_types')->restrictOnDelete();

            $table->date('requested_start_date');

            // Une seule des deux est renseignée, selon duration_unit
            // du type demandé.
            $table->integer('requested_days')->nullable();
            $table->integer('requested_hours')->nullable();

            // Déduite de la durée, jamais saisie par le client.
            $table->date('requested_end_date');

            $table->string('reason');
            $table->enum('status', AbsenceRequest::$STATUS_OPTIONS)
                ->default(AbsenceRequest::$STATUS_OPTIONS[0]);
            $table->dateTime('decision_datetime')->nullable();
            $table->string('decision_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_requests');
    }
};
