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
        Schema::create('export_settings', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name', 255)->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->string('signature_name', 255)->nullable();
            $table->string('signature_title', 255)->nullable();
            $table->text('footer_notes')->nullable();
            $table->timestamps();
        });

        // Insert default row
        DB::table('export_settings')->insert([
            'organization_name' => 'FamilyAid Organization',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_settings');
    }
};
