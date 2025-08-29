<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportTransaksi extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        // Harga
        'harga_valuta',
        'harga_ndpbm',
        'harga_jenis',
        'harga_incoterm',
        'harga_barang',
        'harga_nilai_pabean',
        // Biaya
        'biaya_penambah',
        'biaya_pengurang',
        'biaya_freight',
        'biaya_jenis_asuransi',
        'biaya_asuransi',
        'biaya_voluntary_on',
        'biaya_voluntary_amt',
        // Berat
        'berat_kotor',
        'berat_bersih',
    ];

    protected $casts = [
        'biaya_voluntary_on' => 'boolean',
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
