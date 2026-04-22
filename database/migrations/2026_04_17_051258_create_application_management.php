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
        Schema::create('application_management', function (Blueprint $table) {
            $table->id();
            $table->string('label', 30)->default('Aplikasi Sulut');
            $table->string('deskripsi', 100)->default('Aplikasi di Sulawesi Utara');
            $table->string('route_link', 100)->default('/');
            $table->string('navigation', 30)->comment('react, reload')->default('react');
            $table->boolean('maintenance')->default(0);
            $table->string('maintenance_message', 100)->nullable();
            $table->string('image_path', 50)->default('images/logo/logo-bps.png');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_management');
    }
};
