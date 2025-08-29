<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportEntitas extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        // Importir
        'importir_npwp',
        'importir_nitku',
        'importir_nama',
        'importir_alamat',
        'importir_api_nib',
        'importir_status',
        // Pemusatan
        'pemusatan_npwp',
        'pemusatan_nitku',
        'pemusatan_nama',
        'pemusatan_alamat',
        // Pemilik
        'pemilik_npwp',
        'pemilik_nitku',
        'pemilik_nama',
        'pemilik_alamat',
        // Pengirim
        'pengirim_party_id',
        'pengirim_alamat',
        'pengirim_negara',
        // Penjual
        'penjual_party_id',
        'penjual_alamat',
        'penjual_negara',
    ];

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
