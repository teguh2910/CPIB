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
            $table->string('harga_valuta');
            $table->decimal('harga_ndpbm', 15, 2);
            $table->string('harga_jenis');
            $table->string('harga_incoterm');
            $table->decimal('harga_barang', 15, 2);
            $table->decimal('harga_nilai_pabean', 15, 2);

            // Biaya Lainnya
            $table->decimal('biaya_penambah', 15, 2)->nullable();
            $table->decimal('biaya_pengurang', 15, 2)->nullable();
            $table->decimal('biaya_freight', 15, 2)->nullable();
            $table->string('biaya_jenis_asuransi');
            $table->decimal('biaya_asuransi', 15, 2)->nullable();
            $table->boolean('biaya_voluntary_on')->nullable();
            $table->decimal('biaya_voluntary_amt', 15, 2)->nullable();

            // Berat
            $table->decimal('berat_kotor', 15, 2);
            $table->decimal('berat_bersih', 15, 2);

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
