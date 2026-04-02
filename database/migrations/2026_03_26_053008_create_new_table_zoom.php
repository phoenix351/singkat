<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zooms', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_id');
            $table->unsignedBigInteger('user_id');
            $table->text('topic');
            $table->integer('jumlah_peserta');
            $table->string('bidang');
            $table->string('co_host');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->char('time', 5);
            $table->char('period', 2);
            $table->string('duration');
            $table->timestamps();

            // Kalau mau sekalian foreign key ke users:
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zooms');
    }
};
