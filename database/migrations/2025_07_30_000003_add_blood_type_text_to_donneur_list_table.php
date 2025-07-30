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
        Schema::table('donneur_list', function (Blueprint $table) {
            $table->string('blood_type')->nullable()->after('donor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donneur_list', function (Blueprint $table) {
            $table->dropColumn('blood_type');
        });
    }
};
