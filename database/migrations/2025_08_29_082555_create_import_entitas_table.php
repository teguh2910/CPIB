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

            $table->string('kode_entitas')->nullable();
            $table->string('kode_jenis_identitas')->nullable();
            $table->string('nomor_identitas')->nullable();
            $table->string('nama_identitas')->nullable();
            $table->string('alamat_identitas')->nullable();
            $table->string('nib_identitas')->nullable();
            $table->string('kode_jenis_api')->nullable();
            $table->string('kode_negara')->nullable();
            $table->string('kode_status')->nullable();
            $table->string('no_aju');
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
