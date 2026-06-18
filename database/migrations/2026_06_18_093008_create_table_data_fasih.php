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
        Schema::create('data_fasih', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('subsls_code');
            $table->foreign('subsls_code')->references('code')->on('master_subsls');
            $table->integer('open');
            $table->integer('draft');
            $table->integer('submitted_p');
            $table->integer('approved');
            $table->integer('rejected');
            $table->integer('submitted_r');
            $table->integer('revoked');
            $table->integer('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_fasih');
    }
};
