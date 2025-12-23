<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('blogs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relations
            $table->uuid('company_id');
            $table->uuid('user_id');

            // Titre principal
            $table->string('title');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');

            // Image principale
            $table->string('main_image')->nullable();
            $table->string('main_image_credit')->nullable();

            // === Section 2 ===
            $table->string('second_title')->nullable();
            $table->string('second_image')->nullable();
            $table->enum('second_type', ['horizontal', 'vertical'])->default('horizontal');
            $table->string('second_image_credit')->nullable();
            $table->longText('second_content')->nullable();

            // === Section 3 ===
            $table->longText('third_content')->nullable();
            $table->string('third_image')->nullable();
            $table->string('third_image_credit')->nullable();
            $table->enum('third_type', ['horizontal', 'vertical'])->default('horizontal');

            // === Section 4 ===
            $table->string('fourth_image')->nullable();
            $table->string('fourth_image_credit')->nullable();
            $table->enum('fourth_type', ['horizontal', 'vertical'])->default('horizontal');
            $table->longText('fourth_content')->nullable();

            // Statuts
            $table->boolean('highlighted')->default(false);

            $table->timestamps();

            // FK
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('blogs');
    }
};
