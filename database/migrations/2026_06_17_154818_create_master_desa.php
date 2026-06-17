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
        Schema::create('master_desa', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('label', 100);
            $table->string('kec_code', 7);
            $table->foreign('kec_code')->references('code')->on('master_kec');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_desa');
    }
};
