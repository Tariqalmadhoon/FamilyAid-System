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
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->string('head_national_id', 20)->unique();
            $table->string('head_name', 255);
            $table->foreignId('region_id')->constrained('regions')->restrictOnDelete();
            $table->text('address_text')->nullable();
            $table->enum('housing_type', ['owned', 'rented', 'family_hosted', 'other'])->nullable();
            $table->string('primary_phone', 20)->nullable();
            $table->string('secondary_phone', 20)->nullable();
            $table->enum('status', ['pending', 'verified', 'suspended', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('region_id');
            $table->index('status');
            $table->index('primary_phone');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
