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
        Schema::create('import_pernyataans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('import_notification_id')->nullable()->constrained()->nullOnDelete();

            // Statement declaration
            $table->string('nama_pernyataan', 150);
            $table->string('jabatan_pernyataan', 100)->nullable();
            $table->string('kota_pernyataan', 150);
            $table->date('tanggal_pernyataan')->nullable();
            $table->string('no_aju');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_pernyataans');
    }
};
