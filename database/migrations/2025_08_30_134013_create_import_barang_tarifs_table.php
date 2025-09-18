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
        Schema::create('import_barang_tarifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('import_notification_id')->nullable()->constrained()->nullOnDelete();

            // Tariff fields
            $table->integer('seri_barang');
            $table->integer('kode_tarif');
            $table->integer('tarif');
            $table->integer('kode_fasilitas');
            $table->integer('tarif_fasilitas');
            $table->integer('nilai_bayar');
            $table->string('no_aju');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_barang_tarifs');
    }
};
