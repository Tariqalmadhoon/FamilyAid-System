<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds health/vulnerability classification fields to households.
     */
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->boolean('has_war_injury')->default(false)->after('notes');
            $table->boolean('has_chronic_disease')->default(false)->after('has_war_injury');
            $table->boolean('has_disability')->default(false)->after('has_chronic_disease');
            $table->text('condition_notes')->nullable()->after('has_disability');
            
            // Indexes for filtering
            $table->index('has_war_injury');
            $table->index('has_chronic_disease');
            $table->index('has_disability');
        });

        // Add index on birth_date for child age filtering
        Schema::table('household_members', function (Blueprint $table) {
            $table->index('birth_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropIndex(['has_war_injury']);
            $table->dropIndex(['has_chronic_disease']);
            $table->dropIndex(['has_disability']);
            $table->dropColumn(['has_war_injury', 'has_chronic_disease', 'has_disability', 'condition_notes']);
        });

        Schema::table('household_members', function (Blueprint $table) {
            $table->dropIndex(['birth_date']);
        });
    }
};
