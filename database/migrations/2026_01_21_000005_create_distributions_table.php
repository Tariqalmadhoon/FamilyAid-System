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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->restrictOnDelete();
            $table->foreignId('aid_program_id')->constrained('aid_programs')->restrictOnDelete();
            $table->foreignId('distributed_by')->constrained('users')->restrictOnDelete();
            $table->date('distribution_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Prevent duplicate distribution (same household, same program) unless allow_multiple
            $table->unique(['aid_program_id', 'household_id'], 'unique_program_household');
            
            $table->index('household_id');
            $table->index('aid_program_id');
            $table->index('distributed_by');
            $table->index('distribution_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
