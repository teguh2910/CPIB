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
        Schema::create('import_peti_kemas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('import_notification_id')->nullable()->constrained()->nullOnDelete;
            $table->integer('seri');
            $table->string('nomor_kontainer');
            $table->string('kode_ukuran_kontainer');
            $table->string('kode_jenis_kontainer');
            $table->string('kode_tipe_kontainer');
            $table->timestamps();

            $table->index(['user_id', 'seri']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_peti_kemas');
    }
};
