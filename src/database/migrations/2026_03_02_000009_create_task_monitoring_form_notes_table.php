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
        Schema::create('task_monitoring_form_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_monitoring_id')->constrained('task_monitorings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('notes_remarks')->nullable();
            $table->date('note_date')->nullable();
            $table->timestamps();

            $table->unique(['task_monitoring_id', 'form_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_monitoring_form_notes');
    }
};
