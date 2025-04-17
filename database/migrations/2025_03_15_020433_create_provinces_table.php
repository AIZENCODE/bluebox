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
        Schema::create('provinces', function (Blueprint $table) {
            $table->unsignedBigInteger('idProvince')->primary(); // No autoincremental
            $table->string('province', 100);
            $table->unsignedBigInteger('idDepartment');
            $table->timestamps();

            $table->foreign('idDepartment')
                  ->references('idDepartment')
                  ->on('departments')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
