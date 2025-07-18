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
        Schema::create('districts', function (Blueprint $table) {
            $table->unsignedBigInteger('idDistrict')->primary(); // No autoincremental
            $table->string('district', 100);
            $table->unsignedBigInteger('idProvince');
            $table->timestamps();

            $table->foreign('idProvince')
                  ->references('idProvince')
                  ->on('provinces')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
