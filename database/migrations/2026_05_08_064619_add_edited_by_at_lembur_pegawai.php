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
        //
        Schema::table('lembur_pegawai', function (Blueprint $table) {
            $table->foreignId('edited_by')
                ->after('catatan')
                ->nullable()
                ->constrained('sulutweb_man_management.pegawai')
                ->onUpdate('restrict')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('lembur_pegawai', function (Blueprint $table) {
            $table->dropColumn('edited_by');
        });
    }
};
