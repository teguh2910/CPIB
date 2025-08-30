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
        Schema::table('import_barangs', function (Blueprint $table) {
            $table->decimal('biaya_bm', 15, 2)->nullable()->after('bayar_bm');
            $table->decimal('biaya_ppn', 15, 2)->nullable()->after('bayar_ppn');
            $table->decimal('biaya_pph', 15, 2)->nullable()->after('bayar_pph');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_barangs', function (Blueprint $table) {
            $table->dropColumn(['biaya_bm', 'biaya_ppn', 'biaya_pph']);
        });
    }
};
