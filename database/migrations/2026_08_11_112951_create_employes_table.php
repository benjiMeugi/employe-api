<?php

use App\Models\Employe;
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
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->enum('gender', Employe::$GENDER_OPTIONS);
            $table->date('hire_date');
            $table->boolean('status');
            $table->string('professional_email')->nullable()->unique();
            $table->string('personal_email')->nullable()->unique();
            $table->string('phone_number1')->nullable();
            $table->string('phone_number2')->nullable();
            $table->unsignedBigInteger('title_id');
            $table->foreign('title_id')->references('id')->on('titles');
            $table->unsignedBigInteger('classification_id')->nullable();
            $table->foreign('classification_id')->references('id')->on('classifications');
            $table->unsignedBigInteger('position_id')->nullable();
            $table->foreign('position_id')->references('id')->on('positions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
