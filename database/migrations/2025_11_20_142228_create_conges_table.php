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
        Schema::create('conges', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Entreprise concernée
            $table->foreignUuid('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            // Employé qui demande le congé
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Ticket lié (optionnel)
            $table->foreignUuid('ticket_id')
                ->nullable()
                ->constrained('tickets')
                ->nullOnDelete();

            // Type de congé (aligné sur tes filtres)
            $table->enum('type', [
                'conges_payes',
                'rtt',
                'sans_solde',
                'absence_exceptionnelle',
                'autre',
            ])->default('conges_payes');

            // Période
            $table->date('start_date');
            $table->date('end_date');

            // Durée calculée (en jours ouvrés ou calendaires, à toi de voir
            // quand tu feras la logique - pour l’instant on la laisse nullable)
            $table->unsignedSmallInteger('days_count')->nullable();

            // Statut du traitement par l’admin/RH
            $table->enum('status', [
                'en_attente',
                'valide',
                'refuse',
                'annule',
            ])->default('en_attente');

            // Raison / commentaire de l’employé
            $table->text('reason')->nullable();

            // Commentaire interne RH / admin
            $table->text('admin_comment')->nullable();

            // Qui a validé / refusé
            $table->foreignUuid('validated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};
