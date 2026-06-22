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
        //
        Schema::table('upload_logs', function (Blueprint $table) {
            $table->string('data_type', 3)->after('kode')->default('ppl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('upload_logs', function (Blueprint $table) {
            $table->dropColumn('data_type');
        });
    }
};
