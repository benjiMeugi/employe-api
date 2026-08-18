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
        Schema::create('contracts', function (Blueprint $table) {
    $table->id();
    // CORRECTION ICI : on pointe vers 'employes' au lieu de 'employees'
    $table->foreignId('employee_id')->constrained('employes')->onDelete('cascade'); 
    $table->foreignId('contract_type_id')->constrained('contract_types');
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->string('pay_frequency')->default('Monthly');
    $table->decimal('base_salary', 15, 2);
    $table->enum('status', ['Active', 'Terminated', 'Suspended'])->default('Active');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
