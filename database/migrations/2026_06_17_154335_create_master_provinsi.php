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
        Schema::create('master_provinsi', function (Blueprint $table) {
            $table->string('code', 2)->primary();
            $table->string('label', 100)->default('SULAWESI UTARA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_provinsi');
    }
};
