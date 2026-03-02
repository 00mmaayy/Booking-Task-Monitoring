<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('task_monitorings')->update(['assigned_responsible_person_id' => DB::raw('client_id')]);

        Schema::table('task_monitorings', function (Blueprint $table) {
            $table->dropForeign(['assigned_responsible_person_id']);
            $table->foreign('assigned_responsible_person_id')
                ->references('id')
                ->on('clients')
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if ($firstUserId !== null) {
            DB::table('task_monitorings')
                ->whereNotIn('assigned_responsible_person_id', function ($query) {
                    $query->select('id')->from('users');
                })
                ->update(['assigned_responsible_person_id' => $firstUserId]);
        }

        Schema::table('task_monitorings', function (Blueprint $table) {
            $table->dropForeign(['assigned_responsible_person_id']);
            $table->foreign('assigned_responsible_person_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate();
        });
    }
};
