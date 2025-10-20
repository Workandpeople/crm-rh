<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('societes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('domaine_email')->unique();
            $table->string('logo')->nullable();
            $table->json('politique_conges')->nullable(); // ex : {"cp":25,"rtt":10}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('societes');
    }
};
