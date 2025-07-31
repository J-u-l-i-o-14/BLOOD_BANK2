<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('blood_type_id')->after('donor_id');
            $table->foreign('blood_type_id')->references('id')->on('blood_types');
        });
    }

    public function down(): void
    {
        Schema::table('donation_histories', function (Blueprint $table) {
            $table->dropForeign(['blood_type_id']);
            $table->dropColumn('blood_type_id');
        });
    }
};
