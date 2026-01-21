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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('national_id', 20)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->unsignedBigInteger('household_id')->nullable();
            $table->string('security_question', 255)->nullable();
            $table->string('security_answer_hash', 255)->nullable();
            $table->boolean('is_staff')->default(false);
            $table->unsignedBigInteger('region_id')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('household_id');
            $table->index('region_id');
            $table->index('is_staff');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
