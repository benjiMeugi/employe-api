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
       Schema::create('absence_requests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained('employes')->onDelete('cascade');
    $table->foreignId('absence_type_id')->constrained('absence_types');
    $table->date('requested_start_date');
    $table->date('requested_end_date');
    $table->integer('requested_days_count');
    $table->text('reason')->nullable();
    $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
    
    // Gardez UNIQUEMENT cette ligne-là pour l'approbateur (et supprimez l'autre doublon)
    $table->foreignId('approver_id')->nullable()->constrained('employes');
    
    $table->dateTime('decision_datetime')->nullable();
    $table->text('decision_comment')->nullable();
    $table->boolean('is_deductible')->default(true);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_requests');
    }
};
