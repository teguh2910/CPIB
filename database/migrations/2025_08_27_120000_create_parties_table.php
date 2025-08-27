<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pengirim','penjual']); // jenis master
            $table->string('code', 50)->unique();         // kode unik (SUPP001, SELL001, dst.)
            $table->string('name', 150);
            $table->string('address', 255)->nullable();
            $table->string('country', 2)->nullable();     // ISO 2 (ID, JP, SG, ...)
            $table->timestamps();

            $table->index(['type', 'name']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('parties');
    }
};
