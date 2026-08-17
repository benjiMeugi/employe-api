<?php

use App\Models\AbsenceRequest;
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
            $table->foreignId('employe_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('absence_type_id')->constrained('absence_types')->nullOnDelete();
            $table->date('requested_start_date');
            $table->date('requested_end_date');
            $table->double('requested_days_count');
            $table->string('reason');
            $table->enum('status', AbsenceRequest::$STATUS_OPTIONS);
            $table->dateTime('decision_datetime')->nullable();
            $table->string('decision_comment')->nullable();
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
