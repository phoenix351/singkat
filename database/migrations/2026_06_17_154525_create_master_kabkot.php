<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public $connection = 'sulutweb_se2026';
    public function up(): void
    {
        Schema::create('master_kabkot', function (Blueprint $table) {
            $table->string('code', 4)->primary();
            $table->string('label', 100);
            $table->string('provinsi_code', 2);
            $table->foreign('provinsi_code')->references('code')->on('master_provinsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kabkot');
    }
};
