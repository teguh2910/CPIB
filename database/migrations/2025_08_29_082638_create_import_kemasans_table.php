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
        Schema::create('import_kemasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('import_notification_id')->nullable()->constrained()->nullOnDelete();
            // Common fields
            $table->integer('seri');
            // Kemasan fields
            $table->decimal('jumlah_kemasan', 15, 6)->nullable();
            $table->string('kode_kemasan')->nullable();
            $table->string('merek', 100)->nullable();
            $table->string('no_aju');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_kemasans');
    }
};
