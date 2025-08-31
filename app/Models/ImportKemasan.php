<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportKemasan extends Model
{
    protected $guarded = '';

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
