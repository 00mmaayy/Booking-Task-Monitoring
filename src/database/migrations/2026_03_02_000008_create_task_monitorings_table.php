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
        Schema::create('task_monitorings', function (Blueprint $table) {
            $table->id();
            $table->date('date_task_received');
            $table->foreignId('client_id')->constrained('clients')->cascadeOnUpdate();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnUpdate();
            $table->foreignId('assigned_responsible_person_id')->constrained('users')->cascadeOnUpdate();
            $table->json('required_forms_documents')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_monitorings');
    }
};
