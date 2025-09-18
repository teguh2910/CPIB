<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBarangDokumen extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        'seri_barang',
        'seri_dokumen',
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
