<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public $connection = 'sulutweb_se2026';
    public function up(): void
    {
        Schema::create('master_kec', function (Blueprint $table) {
            $table->string('code', 7)->primary();
            $table->string('label', 100);
            $table->string('kabkot_code', 4);
            $table->foreign('kabkot_code')->references('code')->on('master_kabkot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kec');
    }
};
