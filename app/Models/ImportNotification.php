<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImportNotification extends Model
{
    protected $fillable = [
        'user_id',
        'header',
        'entitas',
        'dokumen',
        'pengangkut',
        'kemasan',
        'transaksi',
        'barang',
        'pungutan',
        'pernyataan',
        'status',
    ];

    protected $casts = [
        'header' => 'array',
        'entitas' => 'array',
        'dokumen' => 'array',
        'pengangkut' => 'array',
        'kemasan' => 'array',
        'transaksi' => 'array',
        'barang' => 'array',
        'pungutan' => 'array',
        'pernyataan' => 'array',
    ];

    /**
     * Relationship to the ImportHeader record (stored in separate table).
     */
    public function headerRecord(): HasOne
    {
        return $this->hasOne(ImportHeader::class, 'import_notification_id');
    }
}
