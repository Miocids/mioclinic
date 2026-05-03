<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Populate team_id from the related medical_record
        DB::statement('
            UPDATE treatments t
            INNER JOIN medical_records mr ON mr.id = t.medical_record_id
            SET t.team_id = mr.team_id
        ');

        Schema::table('treatments', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
