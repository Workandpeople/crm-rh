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
        Schema::create('leaves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $table->enum('type', ['CP','SansSolde','Exceptionnel','Maladie']);
            $table->date('start_date');
            $table->date('end_date');

            $table->string('justification_path')->nullable();
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->foreignUuid('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comments')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
