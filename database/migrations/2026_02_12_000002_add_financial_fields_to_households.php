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
            $table->string('payment_account_type', 20)->nullable()->after('previous_area');
            $table->string('payment_account_number', 50)->nullable()->after('payment_account_type');
            $table->string('payment_account_holder_name', 255)->nullable()->after('payment_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn([
                'payment_account_type',
                'payment_account_number',
                'payment_account_holder_name',
            ]);
        });
    }
};

