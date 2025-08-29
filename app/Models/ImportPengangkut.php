<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportPengangkut extends Model
{
    protected $fillable = [
        'user_id',
        'import_notification_id',
        // BC 1.1
        'bc11_no_tutup_pu',
        'bc11_pos_1',
        'bc11_pos_2',
        'bc11_pos_3',
        // Pengangkutan
        'angkut_cara',
        'angkut_nama',
        'angkut_voy',
        'angkut_bendera',
        'angkut_eta',
        // Pelabuhan
        'pelabuhan_muat',
        'pelabuhan_transit',
        'pelabuhan_tujuan',
        'tps_kode',
    ];

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
