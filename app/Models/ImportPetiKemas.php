<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportPetiKemas extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'seri' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function importNotification()
    {
        return $this->belongsTo(ImportNotification::class);
    }
}
