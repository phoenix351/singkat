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
        Schema::create('master_subsls', function (Blueprint $table) {
            $table->string('code', 16)->primary();
            $table->string('label', 100);
            $table->string('sls_code', 14);
            $table->foreign('sls_code')->references('code')->on('master_sls');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_subsls');
    }
};
