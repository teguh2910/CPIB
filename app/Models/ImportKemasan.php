<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportKemasan extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        'type', // 'kemasan' or 'petikemas'
        'seri',
        // Kemasan fields
        'jumlah',
        'jenis_kemasan',
        'merek',
        // Peti Kemas fields
        'nomor',
        'ukuran',
        'jenis_muatan',
        'tipe',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function importNotification(): BelongsTo
    {
        return $this->belongsTo(ImportNotification::class);
    }

    // Scope untuk filter berdasarkan type
    public function scopeKemasan($query)
    {
        return $query->where('type', 'kemasan');
    }

    public function scopePetikemas($query)
    {
        return $query->where('type', 'petikemas');
    }
}
