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
            $table->string('submission_status')->default('pending')->after('required_forms_documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_monitorings', function (Blueprint $table) {
            $table->dropColumn('submission_status');
        });
    }
};
