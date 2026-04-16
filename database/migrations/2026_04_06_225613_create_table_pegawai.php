<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $connection = 'man_management';

    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip_lama', 9)->unique();
            $table->string('nip', 18)->unique();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('golongan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('organisasi',12);
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('foto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
