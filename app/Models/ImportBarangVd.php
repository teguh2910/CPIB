<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBarangVd extends Model
{
    protected $fillable = [
        'user_id',
        'import_barang_id',
        'seri_barang',
        'kode_vd',
        'nilai_barang',
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
