<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBarang extends Model
{
    protected $guarded = [];

    protected $casts = [
        'lartas' => 'boolean',
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
