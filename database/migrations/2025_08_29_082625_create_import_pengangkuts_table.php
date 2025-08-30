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
        Schema::create('import_pengangkuts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('import_notification_id')->nullable()->constrained()->nullOnDelete();

            // BC 1.1 Section
            $table->string('kode_tutup_pu')->nullable();
            $table->string('nomor_bc_11')->nullable();
            $table->string('tanggal_bc_11')->nullable();
            $table->string('nomor_pos')->nullable();
            $table->string('nomor_sub_pos')->nullable();

            // Pengangkutan Section
            $table->string('angkut_cara');
            $table->string('angkut_nama', 120)->nullable();
            $table->string('angkut_voy', 50)->nullable();
            $table->string('angkut_bendera')->nullable();
            $table->date('tanggal_tiba')->nullable();

            // Pelabuhan & Tempat Penimbunan
            $table->string('kode_pelabuhan_muat')->nullable();
            $table->string('kode_pelabuhan_transit')->nullable();
            $table->string('kode_pelabuhan_tujuan')->nullable();
            $table->string('kode_tps')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_pengangkuts');
    }
};
