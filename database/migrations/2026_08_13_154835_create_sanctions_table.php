<?php

use App\Models\Sanction;
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
        Schema::create('sanctions', function (Blueprint $table) {
            $table->foreignId('id')->primary()->constrained('career_events')->cascadeOnDelete();
            $table->enum('sanction_type', Sanction::$SANCTION_TYPE_OPTIONS);
            $table->string('reason');
            $table->bigInteger('duration_days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanctions');
    }
};
