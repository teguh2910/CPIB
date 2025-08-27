<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'header'      => 'array',
        'entitas'     => 'array',
        'dokumen'     => 'array',
        'pengangkut'  => 'array',
        'kemasan'     => 'array',
        'transaksi'   => 'array',
        'barang'      => 'array',
        'pungutan'    => 'array',
        'pernyataan'  => 'array',
    ];
}
