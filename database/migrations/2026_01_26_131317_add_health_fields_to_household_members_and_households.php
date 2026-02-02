<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('household_members', function (Blueprint $table) {
            $table->boolean('has_war_injury')->default(false)->after('birth_date');
            $table->boolean('has_chronic_disease')->default(false)->after('has_war_injury');
            $table->boolean('has_disability')->default(false)->after('has_chronic_disease');
            $table->string('condition_type', 255)->nullable()->after('has_disability');
            $table->text('health_notes')->nullable()->after('condition_type');
            $table->index(['has_war_injury', 'has_chronic_disease', 'has_disability'], 'member_health_flags_idx');
        });

        Schema::table('households', function (Blueprint $table) {
            $table->string('condition_type', 255)->nullable()->after('has_disability');
        });
    }

    public function down(): void
    {
        Schema::table('household_members', function (Blueprint $table) {
            $table->dropIndex('member_health_flags_idx');
            $table->dropColumn(['has_war_injury', 'has_chronic_disease', 'has_disability', 'condition_type', 'health_notes']);
        });

        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn('condition_type');
        });
    }
};
