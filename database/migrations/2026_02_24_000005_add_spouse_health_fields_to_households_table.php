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
        Schema::table('households', function (Blueprint $table) {
            $table->boolean('spouse_has_war_injury')->default(false)->after('spouse_birth_date');
            $table->boolean('spouse_has_chronic_disease')->default(false)->after('spouse_has_war_injury');
            $table->boolean('spouse_has_disability')->default(false)->after('spouse_has_chronic_disease');
            $table->string('spouse_condition_type', 255)->nullable()->after('spouse_has_disability');
            $table->text('spouse_health_notes')->nullable()->after('spouse_condition_type');

            $table->index(
                ['spouse_has_war_injury', 'spouse_has_chronic_disease', 'spouse_has_disability'],
                'household_spouse_health_flags_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropIndex('household_spouse_health_flags_idx');
            $table->dropColumn([
                'spouse_has_war_injury',
                'spouse_has_chronic_disease',
                'spouse_has_disability',
                'spouse_condition_type',
                'spouse_health_notes',
            ]);
        });
    }
};

