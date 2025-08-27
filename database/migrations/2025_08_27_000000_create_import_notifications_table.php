<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('import_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Simpan per-section sebagai JSON agar fleksibel
            $table->json('header')->nullable();
            $table->json('entitas')->nullable();
            $table->json('dokumen')->nullable();
            $table->json('pengangkut')->nullable();
            $table->json('kemasan')->nullable();
            $table->json('transaksi')->nullable();
            $table->json('barang')->nullable();
            $table->json('pungutan')->nullable();
            $table->json('pernyataan')->nullable();

            $table->string('status')->default('draft'); // draft|submitted|approved|rejected (opsional)
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('import_notifications');
    }
};
