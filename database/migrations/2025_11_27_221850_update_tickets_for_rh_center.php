<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('leave_type')->nullable()->after('related_user_id');
            $table->date('leave_start_date')->nullable()->after('leave_type');
            $table->date('leave_end_date')->nullable()->after('leave_start_date');

            $table->enum('expense_type', ['peage','repas','hebergement','km'])
                  ->nullable()->after('leave_end_date');
            $table->decimal('expense_amount', 10, 2)->nullable()->after('expense_type');
            $table->date('expense_date')->nullable()->after('expense_amount');

            $table->string('document_type')->nullable()->after('expense_date');
            $table->date('document_expires_at')->nullable()->after('document_type');

            $table->enum('incident_severity', ['mineur','majeur','critique'])
                  ->nullable()->after('document_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'leave_type',
                'leave_start_date',
                'leave_end_date',
                'expense_type',
                'expense_amount',
                'expense_date',
                'document_type',
                'document_expires_at',
                'incident_severity',
            ]);
        });
    }
};
