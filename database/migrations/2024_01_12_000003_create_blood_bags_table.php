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
        Schema::create('blood_bags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_type_id')->constrained('blood_types');
            $table->foreignId('center_id')->constrained('blood_donation_centers');
            $table->enum('status', ['available', 'reserved', 'used', 'expired'])->default('available');
            $table->timestamps('collection_date');
            $table->timestamp('expiry_date');
            $table->timestamps();
            $table->softDeletes();

            // Index pour optimiser les recherches
            $table->index(['status', 'expiry_date']);
            $table->index('blood_type_id');
            $table->index('center_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_bags');
    }
};
