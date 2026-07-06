<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    protected $connection = 'sulutweb_se2026';
    public function up(): void
    {
        Schema::create('history_ppl', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100);
            $table->string('kabkot_code', 4);
            $table->foreign('kabkot_code')->references('code')->on('master_kabkot');
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
        Schema::create('history_pml', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100);
            $table->string('kabkot_code', 4);
            $table->foreign('kabkot_code')->references('code')->on('master_kabkot');
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
        Schema::create('history_wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('subsls_code', 30);
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
        Schema::dropIfExists('history_ppl');
        Schema::dropIfExists('history_pml');
        Schema::dropIfExists('history_wilayah');
    }
};
