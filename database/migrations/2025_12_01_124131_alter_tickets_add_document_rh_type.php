<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ⚠️ MySQL : on modifie la définition de la colonne ENUM
        DB::statement("
            ALTER TABLE tickets
            MODIFY COLUMN type ENUM(
                'conge',
                'note_frais',
                'document_rh',
                'incident',
                'autre'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        // On revient à l’ancienne définition sans document_rh
        DB::statement("
            ALTER TABLE tickets
            MODIFY COLUMN type ENUM(
                'conge',
                'note_frais',
                'incident',
                'autre'
            ) NOT NULL
        ");
    }
};
