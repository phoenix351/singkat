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
        Schema::table('capaians', function (Blueprint $table) {
            $table->integer('bulan',2)->nullable();
            $table->dropUnique(['pegawai_id','tahun','periode']);

        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capaians', function (Blueprint $table) {
            $table->dropColumn('bulan');
            $table->unique(['pegawai_id','tahun','periode']);

        });
        
    }
};
