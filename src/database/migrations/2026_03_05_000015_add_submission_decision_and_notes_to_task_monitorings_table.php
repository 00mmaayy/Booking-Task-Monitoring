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
            $table->string('submission_decision')->nullable()->after('acknowledgement_receipt_reference_number');
            $table->text('submission_notes')->nullable()->after('submission_decision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_monitorings', function (Blueprint $table) {
            $table->dropColumn([
                'submission_decision',
                'submission_notes',
            ]);
        });
    }
};
