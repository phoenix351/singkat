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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('type', 4)->comment('unit, tim, all')->default('all');
            $table->integer('to_role_id')->nullable();
            $table->foreignId('app_id')
                ->nullable()
                ->constrained('application_management') // Nama tabel referensi
                ->onUpdate('restrict')
                ->onDelete('set null');
            $table->string('roles', 20)->default('viewer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
