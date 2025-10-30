<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasTable('users')) {
                $table->foreign('admin_user_id')
                      ->references('id')->on('users')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['admin_user_id']);
        });
    }
};
