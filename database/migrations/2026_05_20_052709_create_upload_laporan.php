<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    protected $connection = 'sulutweb_simple';
    public function up(): void
    {
        Schema::create('laporan_lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lembur_id')
                ->nullable()
                ->constrained('sulutweb_simple.lembur')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_lembur');
    }
};
