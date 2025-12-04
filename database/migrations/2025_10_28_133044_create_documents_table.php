<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('type'); // CNI, Contrat, etc.
            $table->string('file_path'); // Dans la V1 : "pending_upload"

            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();

            $table->boolean('signed')->default(false);
            $table->timestamp('signed_at')->nullable();

            // 🔥 Nouveau statut standardisé
            $table->enum('status', [
                'pending',     // Document envoyé par un employé / créé via backlog
                'validated',   // Document validé par l’admin RH
                'rejected',    // Refusé
                'expired'      // Expiration (ex : CNI expirée)
            ])->default('pending');

            $table->json('metadata')->nullable();

            $table->timestamps();

            // Si tu veux empêcher 2 documents du même type pour le même user :
            $table->unique(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
