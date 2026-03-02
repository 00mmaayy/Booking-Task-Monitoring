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
        Schema::table('task_monitoring_form_notes', function (Blueprint $table) {
            $table->string('note_status')->default('pending')->after('note_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_monitoring_form_notes', function (Blueprint $table) {
            $table->dropColumn('note_status');
        });
    }
};
