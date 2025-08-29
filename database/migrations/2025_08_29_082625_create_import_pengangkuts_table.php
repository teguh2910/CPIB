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
            $table->string('bc11_no_tutup_pu', 50)->nullable();
            $table->integer('bc11_pos_1')->nullable();
            $table->integer('bc11_pos_2')->nullable();
            $table->integer('bc11_pos_3')->nullable();

            // Pengangkutan Section
            $table->string('angkut_cara');
            $table->string('angkut_nama', 120)->nullable();
            $table->string('angkut_voy', 50)->nullable();
            $table->string('angkut_bendera')->nullable();
            $table->date('angkut_eta')->nullable();

            // Pelabuhan & Tempat Penimbunan
            $table->string('pelabuhan_muat')->nullable();
            $table->string('pelabuhan_transit')->nullable();
            $table->string('pelabuhan_tujuan')->nullable();
            $table->string('tps_kode')->nullable();

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
