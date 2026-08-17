<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->foreignId('id')->primary()->constrained('career_events')->cascadeOnDelete();
            $table->foreignId('absence_request_id')->nullable()->constrained('absence_requests')->nullOnDelete();
            $table->foreignId('absence_type_id')->constrained('absence_types')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->double('days_count');
            $table->string('reason');
            $table->boolean('is_deductible')->nullable();
            $table->foreignId('leave_credit_id')->nullable()->constrained('leave_credits')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
