<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    protected $connection = 'sulutweb_man_management';
    
    public function up(): void
    {
        Schema::create('timkerja', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->nullable();
            $table->string('label')->default('Tim Umum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timkerja');
    }
};
