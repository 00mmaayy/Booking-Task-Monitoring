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
        Schema::table('task_monitorings', function (Blueprint $table) {
            $table->date('date_of_submission')->nullable()->after('submission_status');
            $table->string('receiving_officer')->nullable()->after('date_of_submission');
            $table->string('acknowledgement_receipt_reference_number')->nullable()->after('receiving_officer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_monitorings', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_submission',
                'receiving_officer',
                'acknowledgement_receipt_reference_number',
            ]);
        });
    }
};
