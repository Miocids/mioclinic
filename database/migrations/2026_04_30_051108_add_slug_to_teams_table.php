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
        Schema::table('teams', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('phone')->nullable()->after('slug');
            $table->string('address')->nullable()->after('phone');
            $table->string('email')->nullable()->after('address');
            $table->text('description')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['slug', 'phone', 'address', 'email', 'description']);
        });
    }
};
