<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBarang extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        'seri',
        // Basic Info
        'pos_tarif',
        'lartas',
        'kode_barang',
        'uraian',
        'spesifikasi',
        'kondisi',
        'negara_asal',
        'berat_bersih',
        // Quantity & Packaging
        'jumlah',
        'satuan',
        'jml_kemasan',
        'jenis_kemasan',
        // Value & Finance
        'nilai_barang',
        'fob',
        'freight',
        'asuransi',
        'harga_satuan',
        'nilai_pabean_rp',
        'dokumen_fasilitas',
        // BM
        'ket_bm',
        'tarif_bm',
        'bayar_bm',
        // PPN
        'ppn_tarif',
        'ket_ppn',
        'bayar_ppn',
        // PPh
        'ket_pph',
        'tarif_pph',
        'bayar_pph',
    ];

    protected $casts = [
        'lartas' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function importNotification(): BelongsTo
    {
        return $this->belongsTo(ImportNotification::class);
    }
}
