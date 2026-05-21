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
        Schema::create('super_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->string('record_id', 50)->nullable();
            $table->string('action', 10);
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('user_id', 36)->nullable();
            $table->timestamp('logged_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_logs');
    }
};
