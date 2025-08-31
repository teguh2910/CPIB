<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportPengangkut extends Model
{
    protected $guarded = [];

    protected $casts = [
        'angkut_eta' => 'date',
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
