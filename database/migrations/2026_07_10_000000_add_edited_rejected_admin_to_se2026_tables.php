<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom edited_a (Edited by Admin Kabupaten) dan
     * rejected_a (Rejected by Admin Kabupaten) ke semua tabel Se2026.
     */
    public $connection = 'sulutweb_se2026';

    public function up(): void
    {
        // data_fasih (PPL)
        Schema::table('data_fasih', function (Blueprint $table) {
            $table->integer('edited_a')->default(0)->after('completed');
            $table->integer('rejected_a')->default(0)->after('edited_a');
        });

        // data_fasih_pml (PML)
        Schema::table('data_fasih_pml', function (Blueprint $table) {
            $table->integer('edited_a')->default(0)->after('completed');
            $table->integer('rejected_a')->default(0)->after('edited_a');
        });

        // history_ppl
        Schema::table('history_ppl', function (Blueprint $table) {
            $table->integer('edited_a')->default(0)->after('completed');
            $table->integer('rejected_a')->default(0)->after('edited_a');
        });

        // history_pml
        Schema::table('history_pml', function (Blueprint $table) {
            $table->integer('edited_a')->default(0)->after('completed');
            $table->integer('rejected_a')->default(0)->after('edited_a');
        });

        // history_wilayah
        Schema::table('history_wilayah', function (Blueprint $table) {
            $table->integer('edited_a')->default(0)->after('completed');
            $table->integer('rejected_a')->default(0)->after('edited_a');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['data_fasih', 'data_fasih_pml', 'history_ppl', 'history_pml', 'history_wilayah'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->dropColumn(['edited_a', 'rejected_a']);
            });
        }
    }
};
