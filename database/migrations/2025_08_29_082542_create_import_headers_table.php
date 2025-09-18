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
        Schema::create('import_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('import_notification_id')->nullable()->constrained()->nullOnDelete();

            // Header fields
            $table->string('nomor_aju')->unique();
            $table->string('kode_kantor');
            $table->string('kode_jenis_impor');
            $table->string('kode_jenis_prosedur');
            $table->string('kode_cara_bayar');
            $table->string('no_aju');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_headers');
    }
};
