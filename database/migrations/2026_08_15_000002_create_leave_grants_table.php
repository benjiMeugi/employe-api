<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('absence_type_id')->constrained('absence_types')->restrictOnDelete();

            $table->string('period');
            $table->decimal('granted_days', 6, 2);
            $table->date('grant_date');
            $table->date('expiration_date')->nullable();
            $table->timestamps();

            // Clé d'idempotence : le job d'accumulation peut être rejoué
            // autant de fois que nécessaire sans jamais créditer en double.
            $table->unique(['employee_id', 'absence_type_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_grants');
    }
};
