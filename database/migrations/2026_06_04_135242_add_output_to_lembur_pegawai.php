<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public $connection = 'sulutweb_simple';
    public function up(): void
    {
        Schema::table('lembur_pegawai', function (Blueprint $table) {
            //
            $table->text('output')->nullable()->after('jam_pulang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lembur_pegawai', function (Blueprint $table) {
            //
            $table->dropColumn('output');
        });
    }
};
