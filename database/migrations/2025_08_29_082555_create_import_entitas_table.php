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
        Schema::create('import_entitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('import_notification_id')->nullable()->constrained()->nullOnDelete();

            // Importir
            $table->string('importir_npwp', 32);
            $table->string('importir_nitku', 32)->nullable();
            $table->string('importir_nama', 150);
            $table->text('importir_alamat');
            $table->string('importir_api_nib', 50)->nullable();
            $table->string('importir_status');

            // NPWP Pemusatan (optional)
            $table->string('pemusatan_npwp', 32)->nullable();
            $table->string('pemusatan_nitku', 32)->nullable();
            $table->string('pemusatan_nama', 150)->nullable();
            $table->text('pemusatan_alamat')->nullable();

            // Pemilik Barang (optional)
            $table->string('pemilik_npwp', 32)->nullable();
            $table->string('pemilik_nitku', 32)->nullable();
            $table->string('pemilik_nama', 150)->nullable();
            $table->text('pemilik_alamat')->nullable();

            // Pengirim
            $table->foreignId('pengirim_party_id')->constrained('parties')->cascadeOnDelete();
            $table->text('pengirim_alamat');
            $table->char('pengirim_negara', 2);

            // Penjual
            $table->foreignId('penjual_party_id')->constrained('parties')->cascadeOnDelete();
            $table->text('penjual_alamat');
            $table->char('penjual_negara', 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_entitas');
    }
};
