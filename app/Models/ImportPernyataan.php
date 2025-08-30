<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportPernyataan extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        'nama',
        'jabatan',
        'tempat',
        'tanggal',
    ];

    protected $casts = [
        'agree' => 'boolean',
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
