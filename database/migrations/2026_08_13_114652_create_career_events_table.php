<?php

use App\Models\CareerEvent;
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
        Schema::create('career_events', function (Blueprint $table) {
            $table->id();
            $table->date('event_date');
            $table->string('comment')->nullable();
            $table->foreign('employe_id')->references('id')->on('employes');
            $table->enum('gender', CareerEvent::$EVENT_OPTIONS);
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_events');
    }
};
