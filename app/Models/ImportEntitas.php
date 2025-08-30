<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportEntitas extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function importNotification(): BelongsTo
    {
        return $this->belongsTo(ImportNotification::class);
    }

    public function pengirimParty(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'pengirim_party_id');
    }

    public function penjualParty(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'penjual_party_id');
    }
}
