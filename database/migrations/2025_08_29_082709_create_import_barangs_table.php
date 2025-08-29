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
        Schema::create('import_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('import_notification_id')->nullable()->constrained()->nullOnDelete();

            // Basic Info
            $table->integer('seri');
            $table->string('pos_tarif', 20);
            $table->boolean('lartas');
            $table->string('kode_barang', 50)->nullable();
            $table->string('uraian', 500);
            $table->string('spesifikasi', 500)->nullable();
            $table->string('kondisi')->nullable();
            $table->char('negara_asal', 2);
            $table->decimal('berat_bersih', 15, 6);

            // Quantity & Packaging
            $table->decimal('jumlah', 15, 6);
            $table->string('satuan');
            $table->decimal('jml_kemasan', 15, 2)->nullable();
            $table->string('jenis_kemasan')->nullable();

            // Value & Finance
            $table->decimal('nilai_barang', 15, 2);
            $table->decimal('fob', 15, 2)->nullable();
            $table->decimal('freight', 15, 2)->nullable();
            $table->decimal('asuransi', 15, 2)->nullable();
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('nilai_pabean_rp', 15, 2);
            $table->string('dokumen_fasilitas', 150)->nullable();

            // BM (Bea Masuk)
            $table->string('ket_bm')->nullable();
            $table->decimal('tarif_bm', 5, 2)->nullable();
            $table->decimal('bayar_bm', 15, 2)->nullable();

            // PPN
            $table->tinyInteger('ppn_tarif')->nullable();
            $table->string('ket_ppn')->nullable();
            $table->decimal('bayar_ppn', 15, 2)->nullable();

            // PPh
            $table->string('ket_pph')->nullable();
            $table->decimal('tarif_pph', 5, 2)->nullable();
            $table->decimal('bayar_pph', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_barangs');
    }
};
