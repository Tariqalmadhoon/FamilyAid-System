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
            $table->string('spouse_full_name', 255)->nullable()->after('head_name');
            $table->string('spouse_national_id', 20)->nullable()->after('spouse_full_name');
            $table->date('spouse_birth_date')->nullable()->after('spouse_national_id');

            $table->index('spouse_national_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropIndex(['spouse_national_id']);
            $table->dropColumn([
                'spouse_full_name',
                'spouse_national_id',
                'spouse_birth_date',
            ]);
        });
    }
};

