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
        Schema::create('payroll_line_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->enum('nature', ['Earning', 'Deduction']);
            $table->enum('calculation_mode', ['Rate', 'FixedAmount', 'Formula']);
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_subject_to_contributions')->default(false);
            $table->boolean('is_employer_contribution')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_line_types');
    }
};
