<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportPungutan extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        'bm_percent',
        'ppn_percent',
        'pph_percent',
        'bea_masuk',
        'ppn',
        'pph',
        'total_pungutan',
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
