<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportHeader extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        'nomor_aju',
        'kantor_pabean',
        'jenis_pib',
        'jenis_impor',
        'cara_pembayaran',
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
