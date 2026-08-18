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
        Schema::create('leave_credits', function (Blueprint $table) {
            $table->id();
           $table->foreignId('employee_id')->constrained('employes')->onDelete('cascade');

            $table->foreignId('absence_type_id')->constrained('absence_types');
            $table->string('period', 7);
            $table->decimal('acquired_days', 5, 2);
            $table->date('acquisition_date');
            $table->date('expiration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_credits');
    }
};
