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
        Schema::table('orders', function (Blueprint $table) {
            // Ajout du statut de réservation
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'expired'])
                  ->default('pending')
                  ->after('payment_status');
                  
            // Ajout des champs de validation des documents
            $table->boolean('documents_validated')->default(false)->after('documents');
            $table->string('documents_validation_comment')->nullable()->after('documents_validated');
            $table->timestamp('documents_validated_at')->nullable()->after('documents_validation_comment');
            $table->unsignedBigInteger('validated_by')->nullable()->after('documents_validated_at');
            $table->foreign('validated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn([
                'status',
                'documents_validated',
                'documents_validation_comment',
                'documents_validated_at',
                'validated_by'
            ]);
        });
    }
};
