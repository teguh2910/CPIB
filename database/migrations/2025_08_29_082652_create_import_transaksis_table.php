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
        Schema::create('import_transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('import_notification_id')->nullable()->constrained()->nullOnDelete();

            // Harga
            $table->string('kode_valuta');
            $table->string('kode_incoterm');
            $table->string('kode_jenis_nilai');
            $table->decimal('ndpbm', 15, 2);
            $table->string('fob');
            $table->string('nilai_incoterm');
            $table->decimal('nilai_barang', 15, 2);
            $table->decimal('cif', 15, 2);

            // Biaya Lainnya
            $table->decimal('biaya_tambahan', 15, 2)->nullable();
            $table->decimal('biaya_pengurang', 15, 2)->nullable();
            $table->decimal('freight', 15, 2)->nullable();
            $table->string('kode_asuransi');
            $table->decimal('asuransi', 15, 2)->nullable();
            $table->decimal('vd', 15, 2)->nullable();

            // Berat
            $table->decimal('bruto', 15, 2);
            $table->decimal('netto', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_transaksis');
    }
};
