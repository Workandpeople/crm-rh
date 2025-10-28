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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('file_path');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();

            $table->boolean('signed')->default(false);
            $table->timestamp('signed_at')->nullable();

            $table->enum('status', ['valid', 'missing', 'expired', 'pending'])->default('pending');
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->unique(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
