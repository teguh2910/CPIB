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
            $table->string('nomor', 11); // Format: AAAA1234567
            $table->string('ukuran');
            $table->string('jenis_muatan');
            $table->string('tipe');
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
