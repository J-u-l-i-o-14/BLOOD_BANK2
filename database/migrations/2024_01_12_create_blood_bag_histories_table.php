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
        Schema::create('blood_bag_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_bag_id')
                  ->constrained('blood_bags')
                  ->onDelete('cascade');
            $table->string('type');
            $table->text('description');
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['blood_bag_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_bag_histories');
    }
};
