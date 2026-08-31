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
        Schema::create('classification_payroll_line_type', function (Blueprint $table) {
            // Clé étrangère vers votre table classifications existante
            $table->foreignId('classification_id')->constrained('classifications')->onDelete('cascade');
            
            // Clé étrangère vers la table des rubriques de paie
            $table->foreignId('payroll_line_type_id')->constrained('payroll_line_types')->onDelete('cascade');
            
            // Valeur associée à cette rubrique pour cette classification (ex: un montant ou un taux spécifique)
            $table->decimal('value', 15, 2)->nullable();
            
            $table->timestamps();

            // Définition de la clé primaire composite pour éviter les doublons
            $table->primary(['classification_id', 'payroll_line_type_id'], 'class_payroll_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classification_payroll_line_type');
    }
};
