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
            $table->integer('seri_barang');
            $table->string('hs', 20);
            $table->boolean('pernyataan_lartas');
            $table->string('kode_barang', 50)->nullable();
            $table->string('uraian', 500);
            $table->string('spesifikasi_lain', 500)->nullable();
            $table->char('kode_negara_asal', 2);
            $table->decimal('netto', 15, 6);

            // Quantity & Packaging
            $table->decimal('jumlah_satuan', 15, 6);
            $table->string('kode_satuan');
            $table->decimal('jumlah_kemasan', 15, 2)->nullable();
            $table->string('kode_kemasan')->nullable();

            // Value & Finance
            $table->decimal('cif', 15, 2);
            $table->decimal('fob', 15, 2)->nullable();
            $table->decimal('freight', 15, 2)->nullable();
            $table->decimal('asuransi', 15, 2)->nullable();
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('cif_rupiah', 15, 2);
            $table->decimal('ndpbm', 15, 2)->nullable();

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
