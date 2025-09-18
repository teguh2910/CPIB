<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBarangTarif extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        'seri_barang',
        'kode_tarif',
        'tarif',
        'kode_pungutan',
        'kode_fasilitas',
        'tarif_fasilitas',
        'nilai_bayar',
        'no_aju',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function importBarang(): BelongsTo
    {
        return $this->belongsTo(ImportBarang::class);
    }
}
