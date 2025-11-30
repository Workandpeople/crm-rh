<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // JSON pour stocker les infos spécifiques (congé, note de frais, doc RH, etc.)
            $table->json('details')->nullable()->after('description');
        });

        // MySQL : on met à jour l'ENUM type pour ajouter document_rh
        DB::statement("
            ALTER TABLE tickets
            MODIFY COLUMN type ENUM('conge','note_frais','document_rh','incident','autre')
            NOT NULL
        ");
    }

    public function down(): void
    {
        // On remet l'ENUM comme avant
        DB::statement("
            ALTER TABLE tickets
            MODIFY COLUMN type ENUM('conge','note_frais','incident','autre')
            NOT NULL
        ");

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('details');
        });
    }
};
