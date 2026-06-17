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
        Schema::create('master_sls', function (Blueprint $table) {
            $table->string('code', 14)->primary();
            $table->string('label', 100);
            $table->string('desa_code', 10);
            $table->foreign('desa_code')->references('code')->on('master_desa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sls');
    }
};
