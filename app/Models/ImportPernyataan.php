<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportPernyataan extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        'declared_by',
        'jabatan',
        'place_date',
        'ttd_image_path',
        'agree',
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
